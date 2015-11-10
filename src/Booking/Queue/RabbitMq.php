<?php

namespace Booking\Queue;


use Booking\Event\TicketEvent;
use Booking\Worker\MqWorker;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMq
{
    const QUEUE_NAME = "train_to_parse.queue";
    const EXCHEAGE_NAME = "train_to_parse.exchange";
    const QUEUE_WITH_DELAY_NAME = "train_to_parse_wait.queue";
    const EXCHANGE_WITH_DELAY_NAME = "train_to_parse_wait.exchange";

    const ERROR_QUEUE_NAME = "train_to_parse_errors.queue";

    const DELAY = 300; // 5min

    /**
     * @var AMQPStreamConnection
     */
    protected $connection;
    /**
     * @var AMQPChannel
     */
    protected $channel;

    /**
     * @var MqWorker
     */
    private $worker;
    /**
     * @var
     */
    private $host;
    /**
     * @var
     */
    private $port;

    private $user;

    private $pass;

    public function __construct($host, $port, $user, $pass, MqWorker $worker)
    {
        $this->host = $host;
        $this->port = $port;
        $this->worker = $worker;
        $this->user = $user;
        $this->pass = $pass;
        $this->init();
    }


    public function consume()
    {
        $this->channel->basic_consume(self::QUEUE_NAME, '', false, false, false, false, array(
            $this,
            'receive'
        ));
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    /**
     * @param $event
     */
    public function addMessage($event)
    {
        //create a message and publish it
        $queueMessage = new AMQPMessage($this->packMessage($event));
        $this->channel->basic_publish($queueMessage, '', self::QUEUE_NAME);
    }

    /**
     * Get a message from the queue. This should be blocking
     *
     * @param AMQPMessage $msg
     * @return String
     */
    public function receive(AMQPMessage $msg)
    {
        try {
            $event = $this->unpackMessage($msg->body);
            $this->worker->process($event);
            $this->reSend($event);
        } catch (\Exception $e) {
            $time = new \DateTime();
            $error = $time->format("Y-m-d H:i:s") . ": " . $e->getMessage();
            error_log($error, E_USER_ERROR);
            $this->reportError($error);
        }
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
    }

    public function shutdown()
    {
        $this->channel->close();
        $this->connection->close();
    }

    private function init()
    {
        $this->connection = new AMQPStreamConnection($this->host, $this->port, $this->user, $this->pass, '/', false, 'AMQPLAIN', null, 'en_US', 0);
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare(self::QUEUE_NAME, false, true, false, false, false);
        $this->channel->exchange_declare(self::EXCHEAGE_NAME, 'direct');
        $this->channel->queue_bind(self::QUEUE_NAME, self::EXCHEAGE_NAME);

        $this->channel->queue_declare(
            self::QUEUE_WITH_DELAY_NAME,
            false,
            true,
            false,
            true,
            true,
            array(
                'x-message-ttl' => array('I', self::DELAY * 1000),   // delay in seconds to milliseconds
                "x-expires" => array("I", self::DELAY * 1000 + 1000),
                'x-dead-letter-exchange' => array('S', self::EXCHEAGE_NAME) // after message expiration in delay queue, move message to the right.now.queue
            )
        );
        $this->channel->exchange_declare(self::EXCHANGE_WITH_DELAY_NAME, 'direct');
        $this->channel->queue_bind(self::QUEUE_WITH_DELAY_NAME, self::EXCHANGE_WITH_DELAY_NAME);

        $this->channel->basic_qos(null, 1, null);

        register_shutdown_function([
            $this,
            'shutdown'
        ], $this->channel, $this->connection);
    }


    private function reportError($message)
    {
        try {
            $this->channel->queue_declare(self::ERROR_QUEUE_NAME, false, true, false, false);
            $msg = new AMQPMessage($message, array('content_type' => 'text/plain', 'delivery_mode' => 2));
            $this->channel->basic_publish($msg, '', self::ERROR_QUEUE_NAME);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    private function packMessage(TicketEvent $event)
    {
        return base64_encode(serialize($event));
    }

    private function unpackMessage($msg)
    {
        return unserialize(base64_decode($msg));
    }

    private function reSend(TicketEvent $event)
    {
        if ($event->getDate()->add(new \DateInterval('P1D')) > new \DateTime()) {
            $this->channel->basic_publish(new AMQPMessage($this->packMessage($event)), '', self::QUEUE_WITH_DELAY_NAME); // re-parse after
        }
    }
}
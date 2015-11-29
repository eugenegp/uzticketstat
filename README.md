Select example

    SELECT last("value") AS "value" 
    FROM "tickets.amount" 
    WHERE
        "train_type_letter" = 'К' AND 
        "train_date_day" = '20160103' AND 
        "train_num" = '072К' AND 
        "train_from_station" = 'Киев-Пассажирский' AND 
        "train_to_station" = 'Запорожье 1'
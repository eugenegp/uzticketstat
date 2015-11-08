var page = require('webpage').create();
page.onError = function(msg, trace) {
    var msgStack = ['PHANTOM ERROR: ' + msg];
    if (trace && trace.length) {
        msgStack.push('TRACE:');
        trace.forEach(function(t) {
            msgStack.push(' -> ' + (t.file || t.sourceURL) + ': ' + t.line + (t.function ? ' (in function ' + t.function +')' : ''));
        });
    }
    console.error(msgStack.join('\n'));
    phantom.exit(1);
};

page.settings.userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/45.0.2454.101 Chrome/45.0.2454.101 Safari/537.36';
page.open('http://booking.uz.gov.ua/ru/', function (status) {
    if (status !== 'success') {
        console.log('Unable to access network');
    } else {
        var token = page.evaluate(function () {
            return  localStorage.getItem('gv-token');
        });
        console.log(token);
        var cookies = page.cookies;
        for(var i in cookies) {
            var name = cookies[i].name;
            if (name == '_gv_sessid')
            {
                console.log(cookies[i].value);
            }
        }
    }
    phantom.exit();
});

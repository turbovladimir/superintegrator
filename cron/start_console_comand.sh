#!/bin/bash

/usr/local/bin/php7.2 ~/superintergration.tk/php bin/console sender
/usr/local/bin/php7.2 ~/superintergration.tk/php bin/console data:pull --url='https://api.fonbetaffiliates.com/rpc/report/affiliate/dynamic-variables?affiliate=100379&filter[from]=#date_from#&filter[to]=#date_to#&groupBy=daily&page=1&count=100' --method='POST' --period=7 --headers='{"Accept":"application/json","Bearer":"433825e3f940e3d012c7082e510efb2141ff3f0a"}'


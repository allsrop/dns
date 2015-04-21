#!/bin/bash


wget -O- --post-data 'id=1&domain=ONE' http://dns/d/modify > /dev/null
wget -O- --post-data 'domain=1&id=2&source=FFF&dest=OOO&type=A' http://dns/r/modify > /dev/null


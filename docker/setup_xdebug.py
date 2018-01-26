#!/usr/bin/python

from os import system
from shutil import copy
from os.path import isfile, isdir, islink, exists
import os
import socket, fcntl, struct
import sys

def log(text):
	print(text)
	sys.stdout.flush()

def get_ip_address(ifname):
    s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
    return socket.inet_ntoa(fcntl.ioctl(
        s.fileno(),
        0x8915,  # SIOCGIFADDR
        struct.pack('256s', ifname[:15])
    )[20:24])

def setup_xdebug():
	if not os.environ.get("DEBUG") == "1":
		return

	log("Setting up xdebug")
	eth0_ip = get_ip_address('eth0')
	host_ip = ".".join(eth0_ip.split(".")[:3]) + ".1"
	xdebug_ini_path = "docker/php-fpm/xdebug.ini"

	if isfile(xdebug_ini_path):
		copy(xdebug_ini_path, "/usr/local/etc/php/conf.d/xdebug.ini")
		os.system("sed -i '/^xdebug.remote_host=/cxdebug.remote_host=%s' /usr/local/etc/php/conf.d/xdebug.ini" % host_ip)


setup_xdebug()
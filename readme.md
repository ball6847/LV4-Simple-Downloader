# Simple Download Manager

Simple download manager based on Laravel 4 framework.

## Features

- Realtime download progress using Websocket.
- Asynchronous downloading, which means you don't have to stay on page while there are active downloads.
- Manage download behind authentication.

## What you need

- PHP 5.4+ (with `shell_exec` enabled)
- MySQL or Sqlite
- [ZeroMQ](http://zeromq.org/) library and [zmq.so](http://www.zeromq.org/bindings:php) extension installed.
- Redis

## Based on

- [Laravel 4](https://github.com/laravel/framework)
- [Laravel-4-Bootstrap-Starter-Site](https://github.com/ball6847/Laravel-4-Bootstrap-Starter-Site)
- [Latchet](https://github.com/sidneywidmer/Latchet) (Laravel 4 Websocket using [Ratchet](https://github.com/cboden/Ratchet))

## TODO

- Instruction on How to install in Ubuntu VPS Server.
- Flexible queue and simultaneous download.
- Stop, start or pause active download.
- Bulk action (remove, add download urls).
- Use attachment filenane header as possible.
- Rename file while downloading.
- Download protection.
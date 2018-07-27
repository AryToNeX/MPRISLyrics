# MPRISLyrics
PoC that displays lyrics in sync with the currently playing song in a MPRIS capable player.

## Before you download

Please note: I am not doing it seriously.
I am only challenging myself here, and I wanted a lyrics visualizer that can run reliably on a terminal.
That's all, don't spam me for ETAs or update requests. If you like the idea, you can always fork it and maintain it.
I won't. When my interest in this project will be over, it will be over. There's no way around it.

## Prerequisites

This project relies on Playerctl, a CLI tool that can remotely control MPRIS-capable players.
You can get it [here](https://github.com/acrisci/playerctl/releases/latest).

You will also need PHP (>= 7.1) and cURL extension for PHP (php-curl)

## Using the project

```
git clone https://github.com/AryToNeX/MPRISLyrics
cd MPRISLyrics
php run.php
```

## Apache 2.0 License

```
Copyright 2018 Anthony Calabretta

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
```

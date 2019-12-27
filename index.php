<?php
  # define the c3pixelflut server
  define("HOST", gethostbyname("wall.c3pixelflut.de"));
  define("PORT", 1234);

  # read pixels from file
  $pixels = file(__DIR__."/pixels.txt", FILE_SKIP_EMPTY_LINES);
  if (false !== $pixels) {
    $socket = false;
    do {
      # we need to establish a connection
      if (false === $socket) {
        if (false !== ($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
          if (false !== socket_connect($socket, HOST, PORT)) {
            printf("%s: Connection established.\n", date("c"));
          } else {
            # connection failed
            try {
              socket_close($socket);
            } finally {
              $socket = false;
            }

            # wait a bit before retrying
            sleep(5);
          }
        }
      }

      # we have an established connection
      if (false !== $socket) {
        for ($i = 0; $i < count($pixels); $i++) {
          if (false === socket_write($socket, $pixels[$i])) {
            # try to close the broken socket
            try {
              socket_close($socket);
            } finally {
              $socket = false;
            }

            # do not proceed with the current round
            break;
          }
        }
      }
    } while (true);
  }

<?php
  # define the number of hosts
  define("HOSTCOUNT", 1);

  # define the c3pixelflut server
  define("HOST", gethostbyname("wall.c3pixelflut.de"));
  define("PORT", 1234);

  # get hostid to to split-up calculations
  $hostid = intval(explode("-", gethostname())[1]);

  # read pixels from file
  $pixels = file(__DIR__."/pixels.txt", FILE_SKIP_EMPTY_LINES);
  if (false !== $pixels) {
    # calculate the chunk size
    $chunksize = ceil(count($pixels)/HOSTCOUNT);

    # get subpixels based on the hostid and the chunksize
    $subpixels = array_slice($pixels, ($hostid-1)*$chunksize, $chunksize);
    if (0 < count($subpixels)) {
      # establish a socket connection
      if (false !== ($socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
        try {
          if (false !== socket_connect($socket, HOST, PORT)) {
            do {
              for ($i = 0; $i < count($subpixels); $i++) {
                socket_write($socket, $subpixels[$i]);
              }
            } while (true);
          }
        } finally {
          socket_close($socket);
        }
      }
    }
  }

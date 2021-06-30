<?php

namespace NoLibForIt\API;

class Ping extends Service {

  protected function handle() {
    Answer::json(200,$this->request);
  }


}

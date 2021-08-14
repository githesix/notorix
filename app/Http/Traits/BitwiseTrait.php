<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Traits;

/**
 * Propose des raccourcis pour gérer les statuts binaires
 *
 * @author lsd
 */
trait BitwiseTrait {
    
  public function isFlagSet($flag)
  {
    return (($this->statut & $flag) == $flag);
  }

  public function setFlag($flag, $value)
  {
    if($value)
    {
      $this->statut |= $flag;
    }
    else
    {
      $this->statut &= ~$flag;
    }
  }
}
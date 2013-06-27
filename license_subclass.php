<?
/*=========================================================================================================
For some special print functions, we ceated this subclass from license class
=========================================================================================================*/

    class license_square extends license
    {

       public function create_dmx($param_array, $state_code="")
       {
            if($this->bDebug)  echo "Start of License Tag";
            print_array($param_array, "param_array(license_square)", $this->bDebug);
            $bAddHOL = 0;

            if($this->bDebug)
            {
                echo "  create_dmx - bAddHOL = ".$bAddHOL. "<br>";
            }

            $bAddHOL = $this->check_for_HOL($param_array);

            $dmx_text = $this->parse_privs($param_array, "DMX", $bAddHOL, $state_code);
            return $dmx_text;
        }

        public function create_pdf($param_array)
        {
            $bAddHOL = 0;
            $bAddHOL = $this->check_for_HOL($param_array);
                if($this->bDebug)
                {
                    echo "  create_pdf - bAddHOL = ".$bAddHOL. "<br>";
                }

            $this->pdf_file = $this->parse_privs($param_array, "PDF", $bAddHOL);
            return $this->pdf_file;
        }

        public function check_for_HOL($param_array)
        {
            $bAddHOL = 0;
            $num_privs = count($param_array['priv_array']);
            if($this->bDebug)
            {
                echo "  enter check_for_HOL <br>";
                echo " num_privs = ".$num_privs." <br>";
            }

            //for loop for each item
            for($priv_count = 0; $priv_count < $num_privs; $priv_count++)
            {
                $current_priv = $param_array['priv_array'][$priv_count];
                if($this->bDebug)
                {
                    echo " priv_count = ".$priv_count." <br>";
                    print_array($param_array, "param_array(license_square in check_for_HOL)", $this->bDebug);
                }
                if($this->bDebug) echo "current landonwer_ind = ".$current_priv['landowner_ind']. "<br>";
                if  ($current_priv['landowner_ind'] == 3)
                {
                    $bAddHOL = 1;
                    if($this->bDebug)
                    {
                        echo "  check_for_HOL - bAddHOL = ".$bAddHOL. "<br>";
                    }
                }
            }
            if($this->bDebug)
            {
                echo "  check_for_HOL - return - bAddHOL = ".$bAddHOL. "<br>";
            }
            return $bAddHOL;
  	}
    }


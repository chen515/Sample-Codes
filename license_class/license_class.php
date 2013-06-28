<?php
/*==============================================================================
A class to control license output

==============================================================================*/
    class license
    {
        public $bDebug;
        public $pdf_file;
        public $dmx_text;

        private db;
        
        public function __construct($bDebug = 0)
        {
            $this->bDebug = $bDebug;
        }

        public function get_work_enviroment()
        {
            global $ENV;
            if($ENV == "prod")
            {
                return 0;               
            }
            else if ($ENV == "test")
            {
                return 1;   
            }
            else 
            {
                return 2;   
            }            
        }

        public function db_connect($server='', $username='', $password='', $database='')
        {
          //setup connection
        	$this->db = mysql_connect($server, $username, $password);
        	if (!$this->db) {
        		die('Could not connect: ' . mysql_error());
        	}
        		
        	//select database
        	mysql_select_db($database, $this->db) or die('Could not select database.');
        }
        
        
        public function retrieve_license_data($tran_id)
        {
        	$sql = "SELECT c.first_name, c.last_name, c.dob, t.license_title, t.tran_amount FROM cust c inner join tran t on c.cid=t.cid where c.cid= $cust_id";
        	$result = mysql_query($sql,$this->db);
        	$row = mysql_fetch_array($result, MYSQL_ASSOC);
        	return $row;
        }
        
        public function db_close()
        {
			return mysql_close($this->db);
        }
        
        // write the xfdf file to drive
        public function write_fdf_file($filename, $filedata)
        {
            // Create the fdf file
            if($fp=fopen($filename,'w'))
            {
                fwrite($fp,$filedata,strlen($filedata));
                $CREATED=TRUE;
            }
            else
            {
                error_page("PDF Generation Error", "Unable to create file: $filename", $this->bDebug, "javascript:window.close();");
                $CREATED=FALSE;
            }

            fclose($fp);
            return $CREATED;
        }

        public function delete_fdf_file($filename)
        {
            if($this->bDebug)
            {
                //if in debug mode don't delete file
                return $filename;
            }
            else
            {
                if(is_file($filename))
                {
                    return unlink($filename);
                }
            }

            print_debug("file $filename does not exist", $this->bDebug);
        }

        // read the dmx file from disk
        public function read_dmx_file($filename)
        {
            $dmx_text = "";

            // Create the fdf file
            if($fp=fopen($filename,'r'))
            {
                $dmx_text = fread($fp, filesize($filename));
            }
            else
            {
                error_page("DMX read Error", "Unable to read file: $filename", $this->bDebug, "javascript:window.close();");
            }

            fclose($fp);
            return $dmx_text;
        }

        // call the FDFMerege app to create a new pdf from the fdf info and the pdf template
        public function createSingle_xPDF($filename, $PDF_Template, $xfdf_file)
        {
            $command_str = "/fdfmerge/fdfmerge -s -x -l logfile.txt -o " . $filename . " " . $PDF_Template ." ".$xfdf_file;
            print_debug($command_str, $this->bDebug);
            $result_msg = shell_exec($command_str);
            print_debug($result_msg, $this->bDebug);
        }

        public function get_date_time_stamp()
        {
            return date("mdY_His") . "_" . substr(microtime(), 3, 7);
        }

            // call the AppendPDF app to create a single PDF from the list of files passed in
        public function append_files($filename_list,$tran_num,$state_abbrev = 'unset',$appendpdf_loc='unset')
        {
        	if(!isset($global_thisstate))
        	{
        		$global_thisstate = '';
        	}
        	
			//PA ITS #46248: use state_abbrev if passed in otherwise use config variable
			if ($state_abbrev == 'unset')
			{ 
				$new_pdf_path = substr($_SERVER['DOCUMENT_ROOT'], 0, strpos($_SERVER['DOCUMENT_ROOT'], "htdocs",0)). 'pdf/'. $global_thisstate.'/';
			}
			else
			{
				$new_pdf_path = substr($_SERVER['DOCUMENT_ROOT'], 0, strpos($_SERVER['DOCUMENT_ROOT'], "htdocs",0)). 'pdf/'. $state_abbrev.'/';
			}

            // create file name
            $base_file_name = $tran_num."_LIC_". $this->get_date_time_stamp();

            $pdf_file = $new_pdf_path . "PDF_work/".$base_file_name.".pdf";
            $xml_file = $new_pdf_path . "aXML/".$base_file_name.".xml";
            print_debug($pdf_file, $this->bDebug);

            //create append.xml file with base_file_name value
            $strXMLText = $this->Build_Append_XML_Text($filename_list, $pdf_file);

            //write append.xml to disk
            $status = $this->write_fdf_file($xml_file, $strXMLText);

            // shell out to run command
            //JIRA-146248: new box has appendpdf under different dir
            if($appendpdf_loc == 'unset')
            {
            	$command_str = "/usr/src/AppendPDF_42_LINUX7/appendpdf " . $xml_file;
            }
            else
            {
            	$command_str = $appendpdf_loc . $xml_file;	
            }
            
			if($this->bDebug)
			{
				echo 'new_pdf_path: ' . $new_pdf_path . '<br>';
				echo 'global_thisstate: ' . $global_thisstate . '<br>';
				echo 'appendpdf_loc in license_class: ' . $appendpdf_loc . '<br>';
				echo 'fdfmerge_location: ' . $fdfmerge_location . '<br>';
				echo 'securepath: ' . $securepath . '<br>';
				echo 'config_location: ' . $config_location . '<br>';
				echo 'command_str: ' . $command_str . '<br>';
			}            
            
            print_debug($command_str, $this->bDebug);
            $result_msg = shell_exec($command_str);
            print_debug($result_msg, $this->bDebug);

            // delete the temp xml file
            $this->delete_fdf_file($xml_file);

            return $pdf_file;

        }

        public function Build_Append_XML_Text($filename_list,$filename)
        {
            $strAppendXL = "";

            $strAppendXL = "<appendparam version=\"1.0\"> \n";

            // output file section
            $strAppendXL = $strAppendXL . "    <outputpdf> \n";
            $strAppendXL = $strAppendXL . "        <file> \n";
            $strAppendXL = $strAppendXL . "            ".$filename." \n";
            $strAppendXL = $strAppendXL . "        </file> \n";
            $strAppendXL = $strAppendXL . "    </outputpdf> \n";

            // input files section
            $strAppendXL = $strAppendXL . "    <sourcepdfs> \n";
            foreach ($filename_list as $src_file)
            {
                $strAppendXL = $strAppendXL . "        <inputpdf> \n";
                $strAppendXL = $strAppendXL . "            <file> \n";
                $strAppendXL = $strAppendXL . "            ".$src_file." \n";
                $strAppendXL = $strAppendXL . "            </file> \n";
                $strAppendXL = $strAppendXL . "        </inputpdf> \n";
            }
            $strAppendXL = $strAppendXL . "    </sourcepdfs> \n";

            // end the xml
            $strAppendXL =  $strAppendXL . "</appendparam> \n";

            return $strAppendXL;
        }

        public function loadImage($filepath,$filename)
        {
            $file = $filepath.$filename;
            $image_text = file_get_contents("$file");
            return $image_text;
        }

        public function deleteFiles($dir,$minutes,$bDebug)
        {
            if ($handle = opendir($dir)) 
            {
                while (false !== ($file = readdir($handle))) 
                {
                    if ($file != "." && $file != "..") 
                    {
                        if(is_file($dir.$file) && substr($file,0,1) != ".")
                        {
                            $fileatime_year    = date("Y",fileatime($dir.$file));
                            $fileatime_month   = date("m",fileatime($dir.$file));
                            $fileatime_date    = date("d",fileatime($dir.$file));
                            $fileatime_hour    = date("H",fileatime($dir.$file));
                            $fileatime_minutes = date("i",fileatime($dir.$file));
                            $fileatime_seconds = date("s",fileatime($dir.$file));
    
                            $fileatime_plus_minutes = date("YmdHis",mktime($fileatime_hour,($fileatime_minutes+$minutes),$fileatime_seconds,$fileatime_month,$fileatime_date,$fileatime_year));
                            $ctime = date("YmdHis");
    
                            if (strlen($minutes) && $minutes > 0)
                            {
                                //if current time is > than filetime+minutes than delete
                                if ($fileatime_plus_minutes < $ctime)
                                {
                                    unlink($dir.$file);
                                    if ($bDebug)
                                    {
                                        echo "File Deleted = $file <br>\n";
                                        echo "File Time (+$minutes Minutes) = $fileatime_plus_minutes <br>\n";
                                        echo "Todays Time  = $ctime <br>\n";
                                    }
                                }
                            }
                            else
                            {
                                unlink($dir.$file);
                                if ($bDebug)
                                {
                                    echo "File Deleted =$file <br>\n";
                                }
                            }
    
                        }
                    }
                }
                closedir($handle);
            }
        }
    } //end class license
?>

<?php
    function fetch_inow_details($endpoint)
    {
       //  $url = 'https://inow.tusc.k12.al.us/API/' . $endpoint;
//		$url = "https://inow.mcpss.com/API/".$endpoint;
		$url = "https://sis-huntsvillecity.chalkableinformationnow.com/api/".$endpoint;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:13.0) Gecko/20100101 Firefox/13.0.1');  // mPDF 5.7.4
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'ApplicationKey: leanfrog B/1F8Y/ToQlRufi/0DgoaKLOBcrd3PpT+wFJL6Sdwy2Z8vZP6GamF7KDmU2nb+Cn/ayElMuxwrWreWae06oNhrCE29gnEizIdFuS3bICs3eFOe7bnRsVyPbPE+4CmOc9QzI5pTbUv9aH/7TrSVVSYcL5WaLzeEwnl2+hlj9c2dw=',
        ));
        curl_setopt ( $ch , CURLOPT_RETURNTRANSFER , 1 );
        curl_setopt($ch, CURLOPT_TIMEOUT, 300); //timeout after 30 seconds
        curl_setopt( $ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY );
        //curl_setopt( $ch, CURLOPT_USERPWD, "LeanFrog_API:9ySjKbFy"); // HCS
        curl_setopt( $ch, CURLOPT_USERPWD, "LeanFrog_API:9ySjKbFy"); // MPS
//        curl_setopt( $ch, CURLOPT_USERPWD, "LeanFrog_API:qs4p2CNu4H4N9g7ETKzF"); //TCS
        $data = curl_exec($ch);
        curl_close($ch);
//		print_r(json_decode($data));
        return json_decode($data);
        //print_r($arr);exit;

    }
	
	function daydiff($datetime1, $datetime2)
	{
		$datetime1 = new DateTime($datetime1);
		$datetime2 = new DateTime($datetime2);
		$interval = $datetime1->diff($datetime2);
		$woweekends = 0;
		
		for($i=0; $i<=$interval->d; $i++){
			//echo $datetime1->format('Y-m-d H:i:s')."<BR>";
			$weekday = $datetime1->format('w');

			if($weekday !== "0" && $weekday !== "6"){ // 0 for Sunday and 6 for Saturday
				$woweekends++;  
			}
		   $datetime1->modify('+1 day');
		 
		}

		/*for($i=0; $i<=$interval->d; $i++){
			//$datetime1->modify('+1 day');
			$weekday = $datetime1->format('w');

			if($weekday !== "0" && $weekday !== "6"){ // 0 for Sunday and 6 for Saturday
				$woweekends++;  
			}

		}*/
		return $woweekends;
	}

?>
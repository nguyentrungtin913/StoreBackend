<?php
namespace App\Helpers;

class APIRender
{
   		public function render($type,$side, $mockup,  $design)
		{
			$png = [
					"obWidth" => 100,
					"obHeight" => 100,
					"left" => 0,
					"top" => 0
					];

			if($type == 'poster_20210804')
			{
				switch ($side) {
					
					case 'vrectangle1':
						$png=[
							"obWidth" => 881,
							"obHeight" => 1319,
							"left" => 352,
							"top" => 193
						];
						break;
					case 'vrectangle2':
						$png=[
							"obWidth" => 644,
							"obHeight" => 955,
							"left" => 479,
							"top" => 49
						];
						break;
					case 'vrectangle3':
						$png=[
							"obWidth" => 690,
							"obHeight" => 1020,
							"left" => 500,
							"top" => 115
						];
						break;
					

					case 'hrectangle1':
					$png=[
						"obWidth" => 1220,
						"obHeight" => 813,
						"left" => 170,
						"top" => 188
					];
					break;

					case 'hrectangle2':
					$png=[
						"obWidth" => 841,
						"obHeight" => 565,
						"left" => 370,
						"top" => 123
					];
					break;

					case 'hrectangle3':
					$png=[
						"obWidth" => 987,
						"obHeight" => 664,
						"left" => 305,
						"top" => 135
					];
					break;
				}
			}
			else if($type == 'puzzle_20210804'){
				switch ($side) {
					
					case '30pcs':
						$png=[
							"obWidth" => 990,
							"obHeight" => 784,
							"left" => 325,
							"top" => 279
						];
						break;
					case '110pcs':
						$png=[
							"obWidth" => 991,
							"obHeight" => 783,
							"left" => 322,
							"top" => 280
						];
						break;
					case '252pcs':
						$png=[
							"obWidth" => 981,
							"obHeight" => 773,
							"left" => 327,
							"top" => 284
						];
						break;
					case '500pcs':
						$png=[
							"obWidth" => 993,
							"obHeight" => 720,
							"left" => 322,
							"top" => 310
						];
						break;
					case '1000pcs':
					$png=[
						"obWidth" => 999,
						"obHeight" => 668,
						"left" => 319,
						"top" => 336
					];
					break;
				}
			}
			else if ($type =="doormat_20210908")
			{
	            switch ( $side )
	            {
	                case "rectangle1":  // 24x16
	                    $png = [
	                        "obWidth" => 975,
							"obHeight" => 642,
							"left" => 322,
							"top" => 341
	                    ];
	                    break;
	                case "rectangle2":  // 24x16
	                    $png = [
	                    	"obWidth" => 1168,
							"obHeight" => 776,
							"left" => 220,
							"top" => 92
	                    ];
	                    break;
	                case "rectangle3":  // 24x16
	                    $png = [
	                    	"obWidth" => 1172,
							"obHeight" => 785,
							"left" => 210,
							"top" => 220
	                    ];
	                    break;
	                case "rectangle4":  // 30x18
	                    $png = [
	                    	"obWidth" => 585,
							"obHeight" => 355,
							"left" => 185,
							"top" => 204
	                    ];
	                    break;
	                case "rectangle5":  // 30x18
	                    $png = [
	                    	"obWidth" => 1102,
							"obHeight" => 660,
							"left" => 340,
							"top" => 380

	                    ];
	                    break;
	                case "rectangle6":  // 30x18
	                    $png = [
	                    	"obWidth" => 1005,
							"obHeight" => 610,
							"left" => 312,
							"top" => 485
	                    ];
	                    break;
	            }
	        }elseif($type =="petbowl_20210820"){
	        	switch ( $side )
	            {
	                case "petbowl1":
	                    $png = [
	                        "obWidth" => 952,
							"obHeight" => 490,
							"left" => 378,
							"top" => 878
	                    ];
	                    break;
	            }
	        }

	        $src1 = new \Imagick(realpath($mockup));
	        
	        if($type =="petbowl_20210820")
	        {
	        	$src2 = self::adjustPetBowl($design);
	        }
	        else
	        {
	        	$src2 = new \Imagick(realpath($design));
	        }
	      return self::custom($src1, $src2, $png);
		}

		static public function adjustPetBowl($design)
		{
			$imagick = new \Imagick(realpath($design));
        	$width= $imagick->getImageWidth();
		    $imagick->waveImage(100, $width*2);
		    return $imagick;
		}

		static public function custom($src1, $src2, $png)
		{
			
	        $src2->resizeImage($png['obWidth'], $png['obHeight'], \Imagick::FILTER_LANCZOS,1);

	        // $src1->setImageArtifact('compose:args', "1,0,-0.5,0.5");COMPOSITE_DSTOVER

	        $src1->compositeImage($src2, \Imagick::COMPOSITE_DSTATOP, $png['left'], $png['top']);
	       	return $src1;
		}
}
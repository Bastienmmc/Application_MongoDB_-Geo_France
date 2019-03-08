<?php
class Carte

{
   
    // property declaration
    private $svgheader = <<<EOSVGH
    <svg xmlns="http://www.w3.org/2000/svg"
         viewBox="0 0 %s %s"
         style="background:#FFF">
        <style>
            polygon {stroke:#000;stroke-width:1px;stroke-linejoin:round}
            polygon:hover {stroke:#FFF;fill:#000;stroke-width:2px}
            text {font-size:12px;fill:#000;alignment-baseline:middle;font-family:sans-serif}
            text:hover {fill:#F00;font-weight:bold}
            g circle {stroke:#F00;stroke-width:1;stroke-opacity:0.8;fill:#000;fill-opacity:0.5}
            g.cities circle {stroke:#ff0;stroke-width:1;stroke-opacity:0.8;fill:#F00;fill-opacity:0.8}
            g.cities text {fill:#F00 !important;stroke:#FF0;stroke-width:0.5px}
            
            g:hover circle {fill:#FF0;fill-opacity:1;stroke-opacity:1}
            g text {fill:#000;font-size:20px;opacity:0;alignment-baseline:middle;text-anchor:middle}
            g.regions text {font-size:30px}
            g:hover text {fill:#000;font-weight:bold;opacity:1}
        </style>
EOSVGH;
private $url='';
    private $svgfooter = '</svg>';
    private $color;
	private $RATIO = 0;
	private $LAT_MOY = 0;
	private $TX = 0;
	private $TY = 0;
	private $lon_min = 0;
	private $lon_max = 0;
	private $lat_min = 0;
	private $lat_max = 0;
	private $width = 0;
	private $height = 0;
	private $contours;
    private $ville;
    private $printArray;
    
    // method declaration

    function __construct($contours , $height,$nom, $lon, $lat )
	{
        //trucco per avere più costruttori di classe
        $a = func_get_args(); 
        $i = func_num_args(); 
        if (method_exists($this,$f='__construct'.$i)) { 
            call_user_func_array(array($this,$f),$a); 
        } 
    }
    function __construct5($contours , $height,$nom, $lon, $lat )
	{
        
        
        $this->height=$height;
        $this->contours = $contours;
		$this->setMaxEtMin();
		$this->setRatio();
        $this->setPrintArray();
        $this->setColor();
        $this->setVille($nom, $lon, $lat);    
    }
    function __construct7($contours , $height, $lon_min, $lon_max, $lat_min, $lat_max, $color)
	{
        $this->height=$height;
        $this->contours = $contours;
		$this->setMaxEtMin();
		$this->setRatio();
        $this->setPrintArray();
        $this->setColor($color);
    }

    function __construct11($contours , $height,$nom, $lon, $lat,  $lon_min, $lon_max, $lat_min, $lat_max, $color, $id_dep)
	{
        $this->height=$height;
        $this->contours = $contours;
     
        $this->lon_min = (float)($lon_min);
		$this->lon_max = (float)($lon_max);
		$this->lat_min = (float)($lat_min);
        $this->lat_max = (float)($lat_max);
        
        
        $this->url= sprintf('onclick="location.href =\'./viewdepartements.php?id=%d\'"',$id_dep);

		$this->setRatio();
        $this->setPrintArray();
        $this->setColor($color);
        $this->setVille($nom, $lon, $lat);    
    }

    private function setColor($color='')
	{
        if(empty($color)){
            $colr = [rand(5,14), rand(5,14), rand(5,14)];
            $this->color = sprintf('#%1x%1x%1x', rand(0,1)+$colr[0], rand(0,1)+$colr[1], rand(0,1)+$colr[2]);

        }else{
           
            $this->color = sprintf('#%1x%1x%1x', rand(0,1)+$color[0], rand(0,1)+$color[1], rand(0,1)+$color[2]);
        }
       
    }
    
   
    public function setVille($nom, $lon, $lat )
	{
        $con=$this->projection($lon, $lat);

        $this->ville=array(
            "nom" => $nom ,
            "lon" => $con[0],
            "lat" => $con[1]
        );
	}

	private function setMaxEtMin()
	{
		$xmin = [];
		$xmax = [];
		$ymin = [];
		$ymax = [];
		foreach($this->contours as $pointmono) {
			$pointx = array_column($pointmono, 0);
			$pointy = array_column($pointmono, 1);
			$xmin[] = min($pointx);
			$xmax[] = max($pointx);
			$ymin[] = min($pointy);
			$ymax[] = max($pointy);
		}

		$this->lon_min = (float)min($xmin);
		$this->lon_max = (float)max($xmax);
		$this->lat_min = (float)min($ymin);
		$this->lat_max = (float)max($ymax);
	}

	
	private function projection($lon, $lat)
	{
		return [($this->RATIO * ($lon + $this->TX) * cos($this->LAT_MOY * (M_PI / 180))) , ($this->RATIO * (-$lat + $this->TY)) ];
	}

	
	private function setRatio()
	{
		// Longitude=X, Latitude=Y
		$this->RATIO = $this->height / ($this->lat_max - $this->lat_min);
		$this->LAT_MOY = ($this->lat_max + $this->lat_min) / 2;
		$this->TX = - $this->lon_min;
        $this->TY = $this->lat_max;
        list($this->width,$unused) = $this->projection($this->lon_max, $this->LAT_MOY);
	}

	
	private function setPrintArray()
	{
        // Longitude=X, Latitude=Y
        //projection($lon, $lat)


        $associa = [];
        $i=0;
		foreach($this->contours as $contour) {
			foreach($contour as $con) {
                $con=$this->projection($con[0], $con[1]);
				$associa[$i][] = $con;
            }
            $i++;
        }
        $this->printArray = $associa;
	}


    public function echoPolygon()
	{
        $svg;
        $svg=sprintf($this->svgheader , $this->width, $this->height);
        //fondo trasparente!
        $svg.=sprintf('<rect  x="0" y="0" width="%d" height="%d" stroke="#979797" stroke-width="0" fill="none"></rect>', $this->width, $this->height);
        foreach ($this->printArray as $contour) {
            $svg.=sprintf('<polygon fill="%s" points="'."\n", $this->color);
        
            foreach ($contour as $ptb) {
                $svg.=sprintf(' %d %d', $ptb[0], $ptb[1]);
        }
        $svg.='"/>'."\n";  
  }
        $svg.= sprintf('<g><circle cx="%d" cy="%d" r="5"/>'."\n", $this->ville['lon'],$this->ville['lat']); 
        $svg.= sprintf('<text x="%d" y="%d">%s</text></g>'."\n", $this->ville['lon'],$this->ville['lat'],$this->ville['nom']);
                
        
        $svg.= $this->svgfooter;
        
        echo $svg;
    }
    public function getHeader(){
        return $this->svgheader;
    }
    public function getFooter(){
        return $this->svgfooter;  
    }
    public function getPolygon()
	{
        $svg='';
        //fondo trasparente!
        $svg.=sprintf('<rect  x="0" y="0" width="%d" height="%d" stroke="#979797" stroke-width="0" fill="none"></rect>', $this->width, $this->height);
        foreach ($this->printArray as $contour) {
            $svg.=sprintf('<polygon %s fill="%s" points="'."\n", $this->url, $this->color);
        
            foreach ($contour as $ptb) {
                $svg.=sprintf(' %d %d', $ptb[0], $ptb[1]);
        } 
        $svg.='"/>'."\n";  
  }
       // $svg.= sprintf('<g><circle cx="%d" cy="%d" r="5"/>'."\n", $this->ville['lon'],$this->ville['lat']); 
       // $svg.= sprintf('<text x="%d" y="%d">%s</text></g>'."\n", $this->ville['lon'],$this->ville['lat'],$this->ville['nom']);
                
        
        //$svg.= $this->svgfooter;
        
        return $svg;
	}
}

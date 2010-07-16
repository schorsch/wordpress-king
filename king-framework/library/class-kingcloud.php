<?php
/**
* @desc class to output weighted clouds
* @author georg leciejewski georg@mediaprojekte.de
* @version 0.2
*/
class KingCloud
{
	/*************
	* public vars
	**************/

	/**
	*@param array $content - the content which is to be shown as a weighted cloud / list
    * 		$content[0] =>
	*				[count] => 123
	*				[text] => meine Categorie
	*				[url => http:///my category
	*				[title] => 122 articel in this Category
	*
	*/
	var $content;
	/**
	*@param int $smallest - smallest fontsize
	*/
	var $smallest;
    /**
	*@param int $largest - largest fontsize
	*/
	var $largest;
    /**
	*@param string $unit - unit for the fontsizes
	*/
	var $unit;
    /**
	*@param string $cold - hex value for the coldest content display (like small)
	*/
	var $cold;
    /**
	*@param string $hot - hex value for the hottest content display (like small)
	*/
	var $hot;
    /**
	*@param string $before - html before each each link in the cloud
	*/
	var $before;
    /**
	*@param string $after - html after each link in the cloud
	*/
	var $after;


	/*************
	* internal vars
	**************/
    /**
	*@param int $spread - spreading between highest and lowest content count
	*/
	var $spread;
    /**
	*@param int $fontstep - the stepping between biggest and smalles fontsize
	*/
	var $fontstep;
    /**
	*@param int $max -  highest content count
	*/
	var $max;
    /**
	*@param int $max -  lowest content count
	*/
	var $min;

	/**
	*@desc initalization of class
	*/
	function KingCloud($content,$smallest=50, $largest=200, $unit='%', $cold='', $hot='', $before='', $after='')
	{
		$this->content	= $content;
		$this->smallest	= $smallest;
		$this->largest	= $largest;
		$this->unit		= $unit;
		$this->cold		= $cold;
		$this->hot		= $hot;
		$this->before	= $before;
		$this->after	= $after;
	}

	/**
	*@desc output the cloud. this is the only function needed to be called from outside
	*/
	function output()
	{
		$this->_setSpread();
		$this->_setFontStep();
		$this->_setColors();

    	foreach ($this->content as $link)
		{# format each link

			$fraction 	= $link['count'] - $this->min;

			$fontsize	= $this->smallest + ($this->fontstep * $fraction);

			$this->_setLink($link,$fraction,$fontsize);
		}
	}


/*************************
* protected internal Methods
**************************/

	function _setLink($link, $fraction,$fontsize)
	{
        $color 		= "";
		for ($i = 0; $i < 3; $i++)
		{ # set color
			$color .= dechex($this->colors['coldval'][$i] + ($this->colors['colorstep'][$i] * $fraction));
		}

		$style = "style=\"";
		if ($this->largest != $this->smallest)
		{ # set fontsize
			$style .= "font-size:".round($fontsize).$this->unit.";";
		}

		if ($this->hot != $this->cold)
		{# set color
			$style .= "color:#".$color.";";
		}
		$style .= "\"";
		echo $this->before
			.'<a href="'.$link['url'].'" title="'.$link['title'].'" '.$style.'>'
			.$link['text']
			.'</a>'
			.$this->after
			."\n";
	}

	/**
	* @desc set the number between the highest and lowest post count
	* @param array $results - the results containing the postscount
	*/
	function _setSpread()
	{
	    $counts = array();
	    foreach ($this->content as $link)
		{# assign each category post count to counts array
			$counts[] = $link['count'];
		}
		$this->min 		= min($counts);
		$this->max 		= max($counts);
		$this->spread	= $this->max  - $this->min;
	}

	/**
	* @desc set the fontsize stepping by setting internal var $this->fontstep
	*/
    function _setFontStep()
	{
		if ($this->largest != $this->smallest)
		{# do fontsize spreading
			$fontspread = $this->largest - $this->smallest;
			if ($this->spread != 0)
			{
				$this->fontstep = $fontspread / $this->spread ;
			}
			else
			{
				$this->fontstep = 0;
			}
		}
	}

	function _setColors()
	{
	    if ($this->hot != $this->cold)
		{ # do color spreading between hot and cold
			for ($i = 0; $i < 3; $i++)
			{
				$this->colors['coldval'][]	= hexdec($this->cold[$i]);
				$this->colors['hotval'][]	= hexdec($this->hot[$i]);
				$this->colors['colorspread'][]= hexdec($this->hot[$i]) - hexdec($this->cold[$i]);
				if ($this->spread != 0)
				{
					$colors['colorstep'][] = (hexdec($this->hot[$i]) - hexdec($this->cold[$i])) / $this->spread;
				}
				else
				{
					$this->colors['colorstep'][] = 0;
				}
			}
		}
	}


}
?>

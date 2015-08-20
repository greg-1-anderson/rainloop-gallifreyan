<?php

class GallifreyanPlugin extends \RainLoop\Plugins\AbstractPlugin
{
	var $word = "test";

	/**
	 * @return void
	 */
	public function Init()
	{
	  // Pick a random word
	  $wordlist = __DIR__ . '/wordlist';
	  $f_contents = file($wordlist);
	  $this->word = trim($f_contents[array_rand($f_contents)]);

	  // Some colors to use
	  $black = array(0, 0, 0);
	  $white = array(255, 255, 255);
	  $blue = array(50, 120, 175);

  	// Render our word
  	$this->render($this->word, $this->imgFilePath(), $blue);

		$this->addJs('js/gallifreyan.js');

		$this->addTemplate('templates/Gallifreyan.html');
		$this->addTemplateHook('Login', 'BottomControlGroup', 'Gallifreyan');
	}

	/**
	 * @return array
	 */
	public function configMapping()
	{
		// No preferences for now.
		return array();
	}

	/**
	 * @param bool $bAdmin
	 * @param bool $bAuth
	 * @param array $aConfig
	 *
	 * @return void
	 */
	public function FilterAppDataPluginSection($bAdmin, $bAuth, &$aConfig)
	{
		// Pass data to the Javascript code
		$aConfig['word'] = $this->word;
		$aConfig['gallifreyan_img'] = $this->imgPath();
	}

  public function imgPath() {
  	return 'img/gallifreyan/' . $this->word . '.png';
  }

  public function imgFilePath() {
  	return realpath(__DIR__ . '/../../../../..') . '/' . $this->imgPath();
  }

	function render($word, $imgFile, $colorArray = NULL) {
	  include_once __DIR__ . '/GallifreyanImg.php';

	  $image = imagecreatetruecolor(300, 300);
	 	if (!$colorArray) {
		  $color = imagecolorallocate($image, 255, 255, 255);
	 	}
	 	else {
		  $color = imagecolorallocate($image, $colorArray[0], $colorArray[1], $colorArray[2]);
	 	}

	  $transparent = imagecolorallocate($image, 55, 55, 55);

	  imagecolortransparent($image, $transparent);
	  imagefill($image, 0, 0, $transparent);

	  gallifreyan($image, $color, $word, 112);

	  imagepng($image, $imgFile);
	}
}

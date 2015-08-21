<?php

class GallifreyanPlugin extends \RainLoop\Plugins\AbstractPlugin
{
	var $word = "test";

	/**
	 * @return void
	 */
	public function Init()
	{
	  include_once __DIR__ . '/GallifreyanImg.php';

	  // Pick a random word
	  $wordlist = __DIR__ . '/wordlist';
	  $f_contents = file($wordlist);
	  $this->word = trim($f_contents[array_rand($f_contents)]);

	  // Some colors to use
	  $black = array(0, 0, 0);
	  $white = array(255, 255, 255);
	  $blue = array(75, 175, 255);

  	// Render our word
  	$this->render($this->word, $this->imgFilePath(), 100, $blue);

  	// Render the letters in the solution
  	$this->renderSolution($this->word, $blue);

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
		return array(
			\RainLoop\Plugins\Property::NewInstance('quiz')->SetLabel('Require solving word prior to login')
				->SetType(\RainLoop\Enumerations\PluginPropertyType::BOOL)
				->SetAllowedInJs(true)
				->SetDefaultValue(true),
		);
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
		$aConfig['path'] = $this->imgBasePath();
	}

  public function imgBasePath() {
  	return 'img/gallifreyan';
  }

  public function imgPath() {
  	return $this->imgBasePath() . '/' . $this->word . '.png';
  }

  public function imgDirname() {
  	return realpath(__DIR__ . '/../../../../..');
  }

  public function imgFilePath() {
  	return $this->imgDirname() . '/' . $this->imgPath();
  }

	function render($word, $imgFile, $radius, $colorArray = NULL) {
	  @mkdir(dirname($imgFile));

	  $width = $radius * 3;

	  $image = imagecreatetruecolor($width, $width);
	 	if (!$colorArray) {
		  $color = imagecolorallocate($image, 255, 255, 255);
	 	}
	 	else {
		  $color = imagecolorallocate($image, $colorArray[0], $colorArray[1], $colorArray[2]);
	 	}

	  $transparent = imagecolorallocate($image, 55, 55, 55);

	  imagecolortransparent($image, $transparent);
	  imagefill($image, 0, 0, $transparent);

	  gallifreyan($image, $color, $word, $radius);

	  imagepng($image, $imgFile);
	}

  // Render all of the letters used in the word.
	function renderSolution($word, $colorArray = NULL) {
	  $length = strlen($word);
	  $dir = $this->imgDirname() . '/' . $this->imgBasePath();

	  for ($i=0; $i < $length; $i++) {
	  	$letter = $word[$i];
	  	$filePath = $dir . '/letters/' . $letter . '.png';
	  	//if (!file_exists($filePath)) {
		  	$this->render($letter, $filePath, 33, $colorArray);
			//}
		}
	}
}

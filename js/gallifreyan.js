(function ($, window) {

	$(function () {

		if (window.rl && window.rl && !window.rl.settingsGet('Auth'))
		{
			var
        imgUrl = window.rl.pluginSettingsGet('gallifreyan', 'gallifreyan_img')
        word = window.rl.pluginSettingsGet('gallifreyan', 'word')
			;

      function addGallifreyanWord() {
        // Display the same image twice -- once in the
        // background, blurred, and once in the foreground.
        // Creates a slight "glow".
        $( "#gallifreyan-div" ).append( '<span><img src="'+imgUrl+'" alt="" style="\
  position: relative; top: 0; left: 0;\
  -webkit-filter: saturate(0%) brightness(800%) blur(3px);\
  -moz-filter: saturate(0%) brightness(800%) blur(3px);\
  -o-filter: saturate(0%) brightness(800%) blur(3px);\
  -ms-filter: saturate(0%) brightness(800%) blur(3px);\
  filter: saturate(0%) brightness(800%) blur(3px);"><img src="'+imgUrl+'" alt="" style="\
  position: absolute; top: 0px; left: 0px;"></span>');

        // Animations in this location do not work well;
        // the image does not stay inside its parents' bounds
        // until after the animation is complete.
        $( "#gallifreyan-div" ).show();
      }

      // The template is not available right away; pause
      // briefly (1/10th of a second) before adding our word.
      setTimeout(addGallifreyanWord, 100);
		}

	});

}($, window));


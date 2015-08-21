(function ($, window) {

	$(function () {

		if (window.rl && window.rl && !window.rl.settingsGet('Auth'))
		{
			var
        path = window.rl.pluginSettingsGet('gallifreyan', 'path')
        word = window.rl.pluginSettingsGet('gallifreyan', 'word')
			;

      function gallifreyanHtml(imgUrl) {
        return '<span><img src="'+imgUrl+'" alt="" class="gallifreyanbg" style="\
          position: relative; top: 0; left: 0;"><img src="'+imgUrl+'" alt="" class="gallifreyan" style="\
          position: absolute; top: 0px; left: 0px;"></span>';
      }

      function addGallifreyanWord() {

        imgUrl = path + '/' + word + '.png';

        // Display the same image twice -- once in the
        // background, blurred, and once in the foreground.
        // Creates a slight "glow".
        $( "#gallifreyan-div" ).append(gallifreyanHtml(imgUrl));

        // Animations in this location do not work well;
        // the image does not stay inside its parents' bounds
        // until after the animation is complete.
        $( "#gallifreyan-div" ).show();

        // You don't get to see your login form until
        // you answer the Gallifreyan quiz.
        $( ".input-append" ).hide();
        $( "#gallifreyan-answer" ).show();

        $( "#GallifreyanWord" ).attr("data:word", word);
      }

      function addGallifreyanSolution() {

        var images = jQuery.map(word.split(''), function(letter) {
          imgUrl = path + '/letters/' + letter + '.png';
          return gallifreyanHtml(imgUrl);
        })

        // Display the same image twice -- once in the
        // background, blurred, and once in the foreground.
        // Creates a slight "glow".
        $( "#gallifreyan-div" ).append(images.join(''));

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


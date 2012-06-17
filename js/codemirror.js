jQuery(document).ready(function($) {
	var _codeMirror;
	//setup code-mirror
	$('.customize-section').each(function() {
		var section = $(this);
		var mirror = section.find('textarea.wpec-tc-code-mirror');
		if(mirror.length > 0) {
			attachMirrorListener(section, mirror);
		}
	});
	//set up the mirror when view is expanded
	function attachMirrorListener(section, mirror) {
		var textarea = document.getElementById(mirror.attr('id'));
		var input = $(mirror.data('input'));

		section.click(function() {
			if(section.hasClass('open') && !section.hasClass('mirror-added')) {
				section.addClass('mirror-added');
				_codeMirror = CodeMirror.fromTextArea(textarea, {
					onUpdate : codemirrorcallback,
				});

			}
		});
		function codemirrorcallback() {
			_codeMirror.save();
			input.val(escape(textarea.value));
		}

	}

});

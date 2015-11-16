/**
 * Module: TYPO3/CMS/FormengineExample/PasswordToggler
 * Adds a button to toggle password visibility
 */
define(['jquery'], function($) {
	'use strict';

	var PasswordToggler = {

		initialize: function() {
			$('input.toggle-password-field').each(function() {
				var $this = $(this);

				$this
				.off('blur focus')
				.on('blur focus', function(event) {
					event.stopImmediatePropagation();
				})
				.parent('div.input-group')
				.append(
					$('<span class="input-group-btn">')
					.append(
						$('<label class="btn btn-default">')
						.attr('for', $this.attr('id'))
						.text(FormengineExample.lang['show'])
						.on('click', function() {
							var $this = $(this),
								$input = $('input#' + $this.attr('for'));

							if ($input.attr('type') === 'password') {
								$input.attr('type', 'text');
								$this.text(FormengineExample.lang['hide']);
							} else {
								$input.attr('type', 'password');
								$this.text(FormengineExample.lang['show']);
							}
						})
					)
				)
			});
		}
	};

	$(PasswordToggler.initialize);

	return PasswordToggler;
});




(function ($, Drupal, drupalSettings) {
Drupal.behaviors.timeago = {
  attach: function (context, settings) {
  		// assign The jquery.timeago.js variables
  	    $.timeago.settings['strings']=settings.timeago_variables['settings']['strings'];
  	    $.timeago.settings['refreshMillis']=settings.timeago_variables['settings']['refreshMillis'];
  	    $.timeago.settings['allowFuture']=settings.timeago_variables['settings']['allowFuture'];
        $.timeago.settings['localeTitle']=settings.timeago_variables['settings']['localeTitle'];
        if(settings.timeago_variables['settings']['cutoff']>0)
        	$.timeago.settings['cutoff']=settings.timeago_variables['settings']['cutoff'];        
        $('abbr.timeago, span.timeago, time.timeago', context).timeago();
  }
};
})(jQuery, Drupal, drupalSettings);

window.task_manager.global = {};

window.task_manager.global.init = function() {}


window.task_manager.global.download_file = function( url_to_file, filename ) {
	var url = jQuery('<a href="' + url_to_file + '" download="' + filename + '"></a>');
	jQuery('.wrap').append(url);
	url[0].click();
	url.remove();
};

window.task_manager.global.remove_diacritics = function( input ) {
	var output = "";

	var normalized = input.normalize("NFD");
	var i=0;
	var j=0;

	while (i<input.length)
	{
		output += normalized[j];

		j += (input[i] == normalized[j]) ? 1 : 2;
		i++;
	}

	return output;
};

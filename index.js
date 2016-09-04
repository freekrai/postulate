var fs = require("fs");
var glob = require("glob");
var yaml = require("yamljs");
var path = require("path");

// options is optional
var blueprints = [];
var options = [];
glob("./app/blueprints/*.yaml", options, function (er, files) {
	if( !er ){
		files.forEach( function( file ){
			console.log( '> ' + file );
			var basename = path.basename(file, '.yaml')
			var yamlString = fs.readFileSync(file, 'utf8');
			var doc = yaml.parse(yamlString);
			blueprints[basename] = doc;
		});
	}
	console.log( blueprints );
	console.log("--------");
	console.log( blueprints.post );
})

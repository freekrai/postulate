/*
 * uberDB
 * http://freekrai.github.io/uberDB
 *
 * Copyright (c) 2015 Roger Stringer
 * Licensed under the MIT license.
 */

/*jshint -W027*/
var fs = require('fs');
var merge = require('merge');
var util = {};

util.isValidPath = function(path) {
	return fs.existsSync(path);
};

util.writeToFile = function(outputFilename, content) {
	if (!content) {
		content = [];
	}
	fs.writeFileSync(outputFilename, JSON.stringify(content, null, 0));
};

util.readFromFile = function(file) {
	return fs.readFileSync(file, 'utf-8');
};

util.removeFile = function(file) {
	return fs.unlinkSync(file);
};

util.updateFiltered = function(collection, query, data, multi) {
	// break 2 loops at once - multi : false
	loop: for (var i = collection.length - 1; i >= 0; i--) {
		var c = collection[i];
		for (var p in query) {
			if (p in c && c[p] == query[p]) {
				collection[i] = merge(c, data);
				if (!multi) {
					break loop;
				}
			}
		}
	}
	return collection;
};

util.removeFiltered = function(collection, query, multi) {
	loop: for (var i = collection.length - 1; i >= 0; i--) {
		var c = collection[i];
		for (var p in query) {
			if (p in c && c[p] == query[p]) {
				collection.splice(i, 1);
				if (!multi) {
					break loop;
				}
			}
		}
	}
	return collection;
};

util.finder = function(collection, query, multi) {
	var retCollection = [];
	loop: for (var i = collection.length - 1; i >= 0; i--) {
		var c = collection[i];
		for (var p in query) {
			if (p in c && c[p] == query[p]) {
				retCollection.push(collection[i]);
				if (!multi) {
					break loop;
				}
			}
		}
	}
	return retCollection;
};

util.ObjectQuery = function() {
	this.results = [];
	this.objects = [];
	this.resultIDS = {};
};

util.ObjectQuery.prototype.findAllInObject = function(object, query, isMulti) {
	for (var objKey in object) {
		this.searchObject(object[objKey], query, object[objKey]);
		if (!isMulti && this.results.length == 1) {
			return this.results;
		}
	}

	while (this.objects.length !== 0) {
		var objRef = this.objects.pop();
		this.searchObject(objRef['_obj'], query, objRef['parent']);
		if (!isMulti && this.results.length == 1) {
			return this.results;
		}
	}

	return this.results;
};

util.ObjectQuery.prototype.searchObject = function(object, query, parent) {
	var multiquery = false;
	for (var objKey in object) {
		if (typeof object[objKey] != 'object') {
			var maybe = {}
			for( var queryKey in query ){
				if (query[queryKey] == object[objKey]) {
					maybe[objKey] = object[objKey];
				}else{
					delete maybe[objKey];
				}
			}
			for (var objKey in maybe) {
				if (parent !== undefined) {
					if (this.resultIDS[parent['_id']] === undefined) {
						this.results.push(parent);
						this.resultIDS[parent['_id']] = '';
					}
				} else {
					if (this.resultIDS[object['_id']] === undefined) {
						this.results.push(object);
						this.resultIDS[object['_id']] = '';
					}
				}
			}
		} else {
			var obj = object;
			if (parent !== undefined) {
				obj = parent;
			}
			var objRef = {
				parent: obj,
				_obj: object[objKey]
			};

			this.objects.push(objRef);
		}
	}
};

module.exports = util;
//Code written by Chris Youderian and released under this MIT license: http://opensource.org/licenses/MIT

var windmill=(function(){
		var scale_factor; var sails; var spinning=false; var original_height; var original_width;
		var options={};
		options.sail_color='skyblue';
		options.base_color='gray';
		options.time=3000;
		
		var init=function(){
			var div = document.getElementById("windmill");		
			var width=div.offsetWidth;
			original_width=80;
			original_height=100;
			scale_factor=width/original_width;
			var height=original_height*scale_factor;					
			var transformation='S'+scale_factor+','+scale_factor+',0,0';	
			var paper=Raphael(div, width, height);
			var sails_path="m 39.991721,2.3848034 c -2.402325,0 -4.886834,22.8234656 -5.78125,32.3125006 1.559438,-1.25501 3.623713,-1.96875 5.78125,-1.96875 2.157508,0 4.128063,0.71374 5.6875,1.96875 -0.894416,-9.489035 -3.285189,-32.3125006 -5.6875,-32.3125006 z m -8.84375,37.6875006 c -0.01814,-0.01814 -3.156905,2.265549 -5.40625,3.90625 -12.759299,9.306741 -19.6959742,15.194375 -19.7187502,16.84375 -0.00281,0.254407 0.037591,0.41113 0.375,0.5 0.2095268,0.05526 1.1092743,0.09153 1.625,0 3.4076532,-0.604525 11.3291102,-3.663405 22.5312502,-8.625 3.031875,-1.34288 6.006038,-2.654035 6.15625,-2.75 0.108257,-0.06917 0.153269,-0.08571 -0.28125,-0.28125 -2.460656,-1.108215 -4.337423,-3.48104 -5,-6.09375 -0.257428,-1.01479 -0.367334,-2.041454 -0.28125,-2.9375 0.02728,-0.297763 0.0084,-0.554626 0,-0.5625 z m 17.71875,0 c -0.0057,0.0084 -0.02433,0.179421 0,0.375 0.05849,0.457859 0.09011,1.76253 0,2.28125 -0.528943,3.050279 -2.4347,5.511272 -5.21875,6.84375 -0.228016,0.1091 -0.375,0.25122 -0.375,0.28125 0,0.12456 9.465744,4.32391 14.3125,6.34375 4.84905,2.020822 8.710951,3.469019 11.5625,4.34375 1.346636,0.413077 2.252525,0.69604 3.40625,0.875 0.252592,0.03909 0.803994,-0.04525 1.03125,-0.09375 0.327639,-0.07029 0.510563,-0.238482 0.46875,-0.5 -0.316983,-1.98232 -8.565632,-8.782191 -23.5,-19.5 -0.934162,-0.67042 -1.680528,-1.258198 -1.6875,-1.25 z";			
			var base_path="m 40.141329,35.21641 c -3.34928,0 -6.064411,2.715131 -6.064411,6.064408 0,3.349294 2.715131,6.064426 6.064411,6.064426 3.349293,0 6.064425,-2.715132 6.064425,-6.064426 0,-3.349277 -2.715132,-6.064408 -6.064425,-6.064408 z m 3.221733,14.592499 c -0.0028,-0.0084 -0.114859,0.02433 -0.284126,0.09461 -0.661381,0.276239 -1.854642,0.470145 -3.032212,0.473788 -0.986251,0.0028 -1.324041,-0.0679 -2.179399,-0.284112 -0.56499,-0.142831 -1.025646,-0.206504 -1.042321,-0.18952 -0.07185,0.07395 -2.708241,45.832308 -2.653185,46.051689 0.0336,0.133988 0.3576,0.550704 0.758053,0.947556 1.040055,1.030712 2.295417,1.642517 3.885026,1.895134 0.800386,0.12711 2.487074,0.02413 3.316479,-0.189521 1.504675,-0.387599 3.023439,-1.275671 3.695506,-2.274147 L 46.110995,95.765857 44.784408,72.834768 C 44.035634,60.196617 43.365296,49.819242 43.363062,49.808923 z";			
			var sails_path_scaled=Raphael.transformPath(sails_path, transformation);
			var base_path_scaled=Raphael.transformPath(base_path, transformation);			
			var base=paper.path(base_path_scaled);			
			base.attr({fill: options.base_color, "stroke-width": 0});
			sails=paper.path(sails_path_scaled);
			sails.attr({fill: options.sail_color, "stroke-width": 0});
		}
		
		var start=function(){
			if (spinning){return}else{spinning=true}
			var center_x=(40.04)*scale_factor;
			var center_y=(original_height-(58.51))*scale_factor;				
			var position='R'+360+', '+center_x+', '+center_y;				
			var anim=Raphael.animation({transform: position}, options.time, 'linear').repeat(Infinity); 
			sails.animate(anim);
		}
		
		var stop=function(){
			if (!spinning){return}else{spinning=false}
			sails.stop();
			var current_path=Raphael.transformPath(sails.attr('path'), sails.attr('transform'))
			sails.attr({path: current_path, transform: ''});			
		}

		return {
			start: start,
			stop: stop,
			init: init,
			options: options
		}
}());

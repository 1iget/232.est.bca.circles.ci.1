// JavaScript ///////////////////////////////////////////////////////////////////
// 
// Copyright 2011 ClickFire Media
// 
////////////////////////////////////////////////////////////////////////////////

/**
 * Class Description
 *
 * @langversion Javascript
 *
 * @author Mili Kuo
 * @since  10.31.2011
 */

function Facebook()
{
	var Facebook = (function() {
	
		//--------------------------------------
		//+ PRIVATE CONSTANTS
		//--------------------------------------
		
		/**
		*	@private
		*/
		
		//--------------------------------------
		//+ PRIVATE VARIABLES
		//--------------------------------------
		
		/**
		*	@private
		*/
		var _myVariable;
		/**
		*	@private
		*/
		var _setter;

		//--------------------------------------
		//+ PRIVATE & PROTECTED INSTANCE METHODS
		//--------------------------------------
		
		return {
			
		//--------------------------------------
		//+ PUBLIC CONSTANTS
		//--------------------------------------
			
		/**
		*	@private
		*/	

		//--------------------------------------
		//+ GETTER/SETTERS
		//--------------------------------------
		

		//--------------------------------------
		//+ PUBLIC METHODS
		//--------------------------------------
		
		/**
		*	@private
		*/
	    createCircle: function(){
	    	FB.api(
		       'https://graph.facebook.com/me/bcacircles:join',
		       'post',
		       { circle: "http://samples.ogp.me/308553962613651",
		         privacy: {'value': 'SELF'}
		         //,
		         //tags: ["100004250646791", "100003988000326"]
		         }, 
		       function(response) {
		         if (!response) {
		           alert('Error occurred.');
		         } else if (response.error) {
		          $('#result').html('Error: ' + response.error.message);
		         } else {
		          $('#result').html('<a href=\"https://www.facebook.com/me/activity/' + response.id + '\">' +
		             'Circle created.  ID is ' + response.id + '</a>')
		         }
		       }
		    );
	    },
	    
	    showFriendlist: function(){
	    	FB.api('/me/friends', function(response){
		      if (response && response.data){
		        $(response.data).each(function(index,value){
				
				if(value.name.substr(0,4) == "Sean") console.log(value);
				//get friend's profile picture
		        	FB.api('/'+value.id+'/picture?type=large', function(res){
				      if (res && res.data){
				        $(res.data).each(function(i,v){
				        	friendProfileList.push(v.url);
				        })
				        if(index >= response.data.length-1) $('body').trigger(GOT_FRIEND_LIST);
				      } else {
				        console.log('friend picture goes wrong', response);
				      }
				    });
		        })
		      } else {
		        console.log('friendlist goes wrong', response);
		      }
		    });

	    },
	    
		
		/**
		*	@private
		*/
	    publicMethod: function() {
      		
    	},

		init: function() {
			return this;
		},
		

		
		// kill trailing commas
		EOF:null
	};
	
	// initialize   
	})().init();
	
	return Facebook;
}
function updateNews() {
	var RunStories = setInterval(GetNewsStories,10000); // Sets the run interval to 10sec
}

function GetNewsStories(){
	//*********************************************************
	// Gets the data from the json file and updates the share data elements
	// Ian Holderness
	// V1.0 -
	// Tested -
	//*********************************************************
  console.log("Starting GetNewsStories"); //TESTING POINT 1

  var XHR=createXHR();
	XHR.open("Get","https://newsapi.org/v2/top-headlines?language=en&country=gb&category=business&apiKey=ba3059047e3548fab44689b0b0870d93", true); // File path to new address when adding to the server
	//console.log("Waiting for ready state change"); //TESTING POINT 2
  XHR.onreadystatechange = (function() {
    console.log("Test2");
		var d = new Date();
		if(XHR.readyState==4) {
			if(XHR.status==200) {
				console.log("Got news Data");
				var responsedata=JSON.parse(XHR.responseText); // Stores the json array to be refrenaced later in the code
				//console.log("Got news Data1");
				//console.log(responsedata.articles.length);


				for(i=0;i < responsedata.articles.length;i++){
					var main = document.getElementById("NewsArticles");
					var lastchild=main.children.length-1;
					var timeelements=main.children[lastchild].getElementsByTagName("time");
					var Newspostdate=timeelements[0].getAttribute("datetime");
					var published = SortDate(responsedata.articles[i].publishedAt);
					//console.log("Published:"+published);
						if(Newspostdate > SortDate(responsedata.articles[i].publishedAt)){
								console.log("New News Added");
								var newarticle=document.createElement("article");

								console.log("Published:"+published);
								newarticlestr="<footer><p>Posted on <time datetime='"+published+"'>"+published+" by <em>"+responsedata.articles[i].author+"</em></p></footer>";
								newarticle.innerHTML=newarticlestr;

								main.insertBefore(newarticle, main.childNodes[0] || null);
								var options=document.createElement("p");

								options.innerHTML="Title - "+responsedata.articles[i].title+"<br/><img src='"+responsedata.articles[i].urlToImage+"'><br/>"+responsedata.articles[i].description+"<br/><a href='"+responsedata.articles[i].url+"' target='_blank'>"+responsedata.articles[i].name+"</a>";
								newarticle.appendChild(options);
						}else{
								console.log("Acticle not later then last posted");
						}
				}
			}else if(XHR.status>=400) {
				alert("Could not request data");
			}
		}
	});
  XHR.send();
}

function SortDate(stringToTrim){
	// Used to deal with the ISO 8001 date format from API

	stringToTrim =stringToTrim.replace("T"," "); // Replaces T in the string to a space
	return stringToTrim.replace("Z",""); // Removed Z in the string befoer returning the string back to the function

}

function LoadNewsStories(){
	//*********************************************************
	// Gets the data from the json file and updates the share data elements
	// Ian Holderness
	// V1.0 -
	// Tested -
	//*********************************************************
  console.log("Starting GetNewsStories"); //TESTING POINT 1

  var XHR=createXHR();
	XHR.open("Get","https://newsapi.org/v2/top-headlines?language=en&country=gb&category=business&apiKey=ba3059047e3548fab44689b0b0870d93", true); // File path to new address when adding to the server
	//console.log("Waiting for ready state change"); //TESTING POINT 2
  XHR.onreadystatechange = (function() {
    console.log("Test2");

		if(XHR.readyState==4) {
			if(XHR.status==200) {
			//console.log("Got news Data");
				var responsedata=JSON.parse(XHR.responseText); // Stores the json array to be refrenaced later in the code
				//console.log("Got news Data1");
				//console.log(responsedata.articles.length);
				var main = document.getElementById("NewsArticles");
				for(i=responsedata.articles.length-1;i >0;i--){
					var newarticle=document.createElement("article"); // Creates a new element of Article
					var published = SortDate(responsedata.articles[i].publishedAt);
					console.log("Published:"+published);
					newarticlestr="<footer><p>Posted on <time datetime='"+published+"'>"+published+" by <em>"+responsedata.articles[i].author+"</em></p></footer>";
					newarticle.innerHTML=newarticlestr;
					main.insertBefore(newarticle, main.childNodes[0] || null); // Inserts the newly mad element before the first element or if there are none
					var options=document.createElement("p");
					options.innerHTML="Title - "+responsedata.articles[i].title+"<br/><img src='"+responsedata.articles[i].urlToImage+"'><br/>"+responsedata.articles[i].description+"<br/><a herf='"+responsedata.articles[i].url+"'><a/>";
					newarticle.appendChild(options);
				}
			}else if(XHR.status>=400) {
				alert("Could not request data");
			}
		}
	});
  XHR.send();
}

//If a window scroll event is used the this code will run
window.onscroll = function() {
  console.log("Scroll Triggered")
	var Start = 360;
	var ElmSize =412;
	var Stories = document.getElementById("NewsArticles");
	for(x=0;x < Stories.children.length;x++){
			var disttobottom=getScrollPosition();
			var change = Math.floor((disttobottom+Start) / ElmSize);
			//var Cal =disttobottom+Start;
			//console.log("Children to hide: "+change+" child"+(x+1));
			if(x+1 <= change){
				console.log("Hide");
				Stories.children[x].style.opacity = 0;
			}else{
				console.log("See");
				Stories.children[x].style= "";
			}

	}

}

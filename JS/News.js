function updateNews() {
	var checkPosition = setInterval(GetNewsStories,10000); // Sets the run interval to 10sec
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
				var responsedata=JSON.parse(XHR.responseText);
				console.log("Got news Data1");
				console.log(responsedata.articles.length);
				var main = document.getElementById("NewsArticles");
				//var lastchild=main.childNodes[0];
				//var timeelements=main.children[lastchild].getElementsByTagName("time");
				//var Newspostdate=timeelements[0].getAttribute("datetime");
				for(i=0;i < responsedata.articles.length;i++){
						//if(Newspostdate > responsedata.articles[i].publishedAt){
								console.log(responsedata.articles[i].title);
								var newarticle=document.createElement("article");
								var published = moment(responsedata.articles[i].publishedAt, 'YYYY-MM-DD HH:mm');
								newarticlestr="<footer><p>Posted on <time datetime='"+published+"'>"+published+" by <em>"+responsedata.articles[i].author+"</em></p></footer>";
								newarticle.innerHTML=newarticlestr;

								main.insertBefore(newarticle, main.childNodes[0] || null);
								var options=document.createElement("p");

								options.innerHTML="Title - "+responsedata.articles[i].title+"<br/><img src='"+responsedata.articles[i].urlToImage+"'><br/>"+responsedata.articles[i].description+"<br/><a href='"+responsedata.articles[i].url+"' target='_blank'>"+responsedata.articles[i].name+"</a>";
								newarticle.appendChild(options);
						//}else{
								//console.log("Acticle not eariler then last posted")
						//}
				}
			}else if(XHR.status>=400) {
				alert("Could not request data");
			}
		}
	});
  XHR.send();
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
				console.log("Got news Data");
				var responsedata=JSON.parse(XHR.responseText);
				console.log("Got news Data1");
				console.log(responsedata.articles.length);
				var main = document.getElementById("NewsArticles");
				//moment(ISOStringHere, 'YYYY-MM-DD HH:mm');
				for(i=0;i < responsedata.articles.length;i++){
					var newarticle=document.createElement("article"); // Creates a new element of Article
					newarticlestr="<footer><p>Posted on <time datetime='"+responsedata.articles[i].publishedAt+"'>"+responsedata.articles[i].publishedAt+" by <em>"+responsedata.articles[i].author+"</em></p></footer>";

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

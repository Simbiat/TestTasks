## Simple REST API

### Story
Your task is to create a simple REST API with two endpoints, and a client that interfaces with the API.

### Task Description
#### Endpoints to be implemented
-	/getMovie - An user should be able to pass the title of a movie, the year and the version of the plot. This endpoint should request results from IMDB Open API (https://www.omdbapi.com/) and return the results to the user.
-	/getBook - An user should be able to pass an ISBN number of a book and get all possible information about the book. This endpoint should request results from OpenLibrary API (https://openlibrary.org) and return the results to the user.
#### Conditions
-	All results should be returned in JSON format.
-	REST API should implement JWT web token as a way of authorizing requests.
-	REST API can be done in one of the languages: C#, Java, PHP.
-	The client for this REST API must be done in PHP. The only responsibility of the client is to call the endpoints and display results on the screen.
#### Technical Requirements
-	PHP 8.x / C# / Java / Node.js
-	OOP paradigm used

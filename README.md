# XML-to-WP-Posts-Plugin
XML to WP Posts Plugin - Version 1.0

Homework assignment – 

Background – Develop a feed program to ingest feed from third party sources and render the published content via a WP API endpoint. 

The feed source is  - https://www.nbcnewyork.com/?rss=y&most_recent=y

Minimum requirement 
Develop the processor as a wordpress plugin that any workdpress installation can use. 
The code developed shall be in a git repo that the candidate can commit to and share the repo for evaluation.
The processor shall leverage wordpress scheduler ( choose any that you know about) and shall run every 10 minutes to ingest new articles from the feed. While developing the plugin be mindful that it doesn’t crash if there are too many articles to ingest.  

Feed ingest url - https://www.nbcnewyork.com/?rss=y&most_recent=y
This feed has many elements but just ingest the following for each article.
-	item
-	title
-	media-content
-	description
-	pub date
We shall process 15 article at a time and use some sort of bookmark to retrieve the next 15 articles in the next schedule run. 

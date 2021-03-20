curl http://localhost:9999/scraper -d '{"query":"https://www.zerohedge.com/political/why-hr1-threatens-election-integrity"}' | python3 -m json.tool

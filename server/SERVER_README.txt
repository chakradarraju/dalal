stockdata.php:
    Default:
        Returns JSON object listing all stocks available in database, each stock object will contain all stock details along with the stock's graph data
    ?stockId={stockId}
        Returns JSON object of the specific stock in the GET variable
    ?stockId=Index
        Returns JSON object listing market index values in different times

exchange.php:
    Default:
        Does nothing
    POST: trade, shareId, number, rate
        Sets buy or sell bid, with the above parameters (trade = "buy" or "sell", shareId, number - number of shares in bid, rate - rate per share, at which user is willing to buy/sell)
        Returns result in JSON format
    POST: buyFromExchange, shareId, number
        Buys shares from exchange @ exchangePrice, if available, with the above parameters (buyFromExchange used only for identifying this type of request, shareId, number as in previous request)
        Returns result in JSON format

queuedata.php:
    Default:
        Returns JSON data listing pending buy/sell bids (Listed parameter for each entry will have all columns in buy/sell table, a type parameter which will be "buy"/"sell", used to differentiate between buy/sell bids)
    POST: cancelOrder, type, orderId
        cancels the pending buy/sell bid, where type = "buy"/"sell", orderId = "buyId"/"sellId"
        Returns status in JSON format

userdata.php:
    Default = ?getDetail=portfolio:
        Returns JSON object of all unhidden user data in database, including graph and stocks. graph will have JSON object of all recorded points, stocks will be JSON object containing each stock owned by the user as key=stockId and value=number of shares owned
    ?getDetail=ranklist:
        Generates ranklist and returns it as JSON object

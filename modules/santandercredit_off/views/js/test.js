  
        function ehpGetPprice(){
            let q = $('div.current-price > span[itemprop="price"],div.current-price > span.current-price-value').attr("content");
            return q;
        }
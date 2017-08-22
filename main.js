$(document).ready(function() {
    // Global Construction
    var articleList = new ArticleList();
    var obituaryList = new ObituaryList();
    var weddingList = new WeddingList();
    var printList = new PrintList();
    var items = [];
    loadSessionPrints();
    
    // Print
    function saveSessionPrints() {
        var articles = printList.articleList;
        var obits = printList.obituaryList;
        var weddings = printList.weddingList;
        var data = {
            articles: articles,
            obits: obits,
            weddings: weddings
        };
        window.sessionStorage.data = JSON.stringify(data);
/*        $.ajax({
            url: 'printSession.php',
            type: 'POST',
            data: {data: JSON.stringify(data)},
            success: function(data) {
                console.log(JSON.stringify(data));
            },
            error: function(error) {
                console.log(JSON.stringify(error));
            }
        });*/
    }
    function loadSessionPrints() {
        if(window.sessionStorage.data) {
            var thisData = JSON.parse(window.sessionStorage.data);
            if(thisData.articles.length > 0 || thisData.obits.length > 0 || thisData.weddings.length > 0) {
                printList = new PrintList();
                for(x in thisData.articles) {
                    var item = thisData.articles[x];
                    var article = new Article(item.subject, item.headline, item.date, item.page, item.id);
                    printList.addPrint(article);
                }
                for(x in thisData.obits) {
                    var item = thisData.obits[x];
                    var obit = new Obituary(item.lastname, item.firstname, item.birthdate, item.deathdate, item.obitdate, item.page, item.id);
                    printList.addPrint(obit);
                }
                for(x in thisData.weddings) {
                    var item = thisData.weddings[x];
                    var wedding = new Wedding(item.lastname, item.firstname, item.announcement, item.weddingdate, item.articledate, item.page, item.id);
                    printList.addPrint(wedding);
                }
                $("#printButton").show();
            }
            else {
                printList = new PrintList();
                $(".printButton").hide();
            }
        } else {
            printList = new PrintList();
            $(".printButton").hide();
        }
        
        /*$.ajax({
            url: 'printSession.php',
            success: function(data) {
                debugger;
                var thisData = JSON.parse(data);
                if(thisData.hasPrints) {
                    printList = new PrintList();
                    for(x in thisData.articles) {
                        var item = thisData.articles[x];
                        var article = new Article(item.subject, item.headline, item.date, item.page, item.id);
                        printList.addPrint(article);
                    }
                    for(x in thisData.obits) {
                        var item = thisData.obits[x];
                        var obit = new Obituary(item.lastname, item.firstname, item.birthdate, item.deathdate, item.obitdate, item.page, item.id);
                        printList.addPrint(obit);
                    }
                    for(x in thisData.weddings) {
                        var item = thisData.weddings[x];
                        var wedding = new Wedding(item.lastname, item.firstname, item.announcement, item.weddingdate, item.articledate, item.page, item.id);
                        printList.addPrint(wedding);
                    }
                    $("#printButton").show();
                } else {
                    printList = new PrintList();
                    $("#printButton").hide();
                }
            },
            error: function(error) {

                console.log(JSON.stringify(error));
            }
        });*/
    }
    $("body").on('click', '#printButton', function() {
        if(printList.hasItems) {
            var articleDataTest = [
                {
                    subject: 'Library',
                    headline: 'Library',
                    date: '01/01/1990',
                    page: 'A1'
                }
            ];
            printList.sortLists();
            document.getElementById("articleData").value = JSON.stringify(printList.articleList);
            document.getElementById("obituaryData").value = JSON.stringify(printList.obituaryList);
            document.getElementById("weddingData").value = JSON.stringify(printList.weddingList);
            $("#printForm").submit();
            $("#afterPrintModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $("#afterPrintModal").modal('show');
        }
    });
    $("#afterPrintRemove").click(function() {
        printList.clearPrints();
        saveSessionPrints();
        removePrintButtonIfNoPrints();
        $("#afterPrintModal").modal('hide');
    });
    function loadPrints() {
        var htmlString = '';
        if(printList.hasItems()) {
            htmlString += '<button type="button" data-dismiss="modal" style="float:right;" class="btn btn-success printButton" id="printButton">Print</button>';
        }
        if(printList.articleList.length > 0) {
            htmlString += '<h2 class="text-center">Articles</h2>'
            htmlString += '<table class="table"><thead><tr><th>No.</th><th>Subject</th><th>Headline</th><th>Date</th><th>Page</th><th><button type="button" class="btn btn-primary btn-xs" id="removeAllPrints">Remove All</button></th></thead><tbody>';
            var currentRow = 1;
            for(x in printList.articleList) {
                htmlString += printList.articleList[x].tableRow(currentRow, true);
                currentRow++;
            }
            htmlString += '</tbody></table>';
        }
        if(printList.obituaryList.length > 0) {
            htmlString += '<h2 class="text-center">Obituaries</h2><table class="table"><thead><tr><th>No.</th><th>Last Name</th><th>First Name</th><th>Birth Date</th><th>Death Date</th><th>Obituary Date</th><th>Page</th><th><button type="button" class="btn btn-primary btn-xs" id="removeAllObituaryPrints">Remove All</button></th></thead><tbody>';
            var currentRow = 1;
            for(x in printList.obituaryList) {
                htmlString += printList.obituaryList[x].tableRow(currentRow, true);
                currentRow++;
            }
            htmlString += '</tbody></table>';
        }
        if(printList.weddingList.length > 0) {
            htmlString += '<h2 class="text-center">Weddings/Anniversaries</h2><table class="table"><thead><tr><th>No.</th><th>Last Name</th><th>First Name</th><th>Announcement</th><th>Wedding Date</th><th>Article Date</th><th>Page</th><th><button type="button" class="btn btn-primary btn-xs" id="removeAllWeddingPrints">Remove All</button></th></thead><tbody>';
            var currentRow = 1;
            for(x in printList.weddingList) {
                htmlString += printList.weddingList[x].tableRow(currentRow, true);
                currentRow++;
            }
            htmlString += '</tbody></table>';
        }
        document.getElementById('printsHere').innerHTML = htmlString;
    }
    $("body").on('click', '#viewPrints', function() {
        loadPrints();
        $('#printModal').modal('show');
    });
    $('body').on('hidden.bs.modal', function () {
        if($('.modal.in').length > 0)
        {
            $('body').addClass('modal-open');
        }
    });
    function removePrintButtonIfNoPrints() {
        if(!printList.hasItems()) {
            $("body").find('.printButton').hide();
        } else {
            $('body').find(".printButton").show();
        }
    }
    $("#printsHere").on('click', '.removePrint', function() {
        var type = $(this).data('type');
        var id = $(this).data('id');
        printList.removePrint(id, type);
        $(this).closest('tr').fadeOut();
        loadPrints();
        saveSessionPrints();
        removePrintButtonIfNoPrints();
    });
    $("#printsHere").on('click', '#removeAllPrints', function() {
        printList.articleList = [];
        loadPrints();
        saveSessionPrints();
        removePrintButtonIfNoPrints();
    });
    $("#printsHere").on('click', '#removeAllObituaryPrints', function() {
        printList.obituaryList = [];
        loadPrints();
        saveSessionPrints();
        removePrintButtonIfNoPrints();
    });
    $("#printsHere").on('click', '#removeAllWeddingPrints', function() {
        printList.weddingList = [];
        loadPrints();
        saveSessionPrints();
        removePrintButtonIfNoPrints();
    });
    $("#resultsHere").on('click','.print:enabled', function() {
        var addedPrint = $(this);
        if(addedPrint.data('type') == 'article') {
            var item = articleList.list[addedPrint.attr('id')];
            printList.addPrint(item);
        } else if(addedPrint.data('type') == 'obituary') {
            var item = obituaryList.list[addedPrint.attr('id')];
            printList.addPrint(item);
        } else {
            var item = weddingList.list[addedPrint.attr('id')];
            printList.addPrint(item);           
        }
        addedPrint.addClass("disabled");
        saveSessionPrints();
        removePrintButtonIfNoPrints();
    });
    $("#resultsHere").on('click', '#printAllObits:enabled', function(){
        for(x in obituaryList.list) {
            if(!obituaryList.list[x].printed()) {
                printList.obituaryList.push(obituaryList.list[x]);
                $(this).addClass('disabled');
            }
        }
        $('.print').addClass('disabled');
        saveSessionPrints();
        removePrintButtonIfNoPrints();
    });
    $("#resultsHere").on('click', '#printAllArticles:enabled', function(){
        for(x in articleList.list) {
            if(!articleList.list[x].printed()) {
                printList.articleList.push(articleList.list[x]);
                $(this).addClass('disabled');
            }
        }
        $('.print').addClass('disabled');      
        saveSessionPrints(); 
        removePrintButtonIfNoPrints(); 
    });
    $("#resultsHere").on('click', '#printAllWeddings:enabled', function(){
        for(x in weddingList.list) {
            if(!weddingList.list[x].printed()) {
                printList.weddingList.push(weddingList.list[x]);
                $(this).addClass('disabled');
            }
        }
        $('.print').addClass('disabled');  
        saveSessionPrints(); 
        removePrintButtonIfNoPrints();     
    });

    // Subjects
    function search(searchterm) {
        var searched = items.filter(findElement);
        $("#notSelected").find(".subjectItem").hide();
        for(x in searched) {
            if(searched[x].added == false) {
                $("#notSelected").find("#"+searched[x].id).show();
            }
        }
        function findElement(input) {
            var uInput = input.subject.toUpperCase();
            var uSearchterm = searchterm.toUpperCase();
            return uInput.includes(uSearchterm);
        }
    }
    function createList() {
        var unselected = "";
        var selected = "";
        for(x in items) {
            unselected += items[x].card();
            selected += items[x].card();
        }
        document.getElementById("notSelected").innerHTML = unselected;
        $("#notSelected").find(".subjectItem").addClass("label-default").hide();
        document.getElementById("isSelected").innerHTML = selected;
        $("#isSelected").find(".subjectItem").addClass("label-success").hide();
        document.getElementById("subjectList").innerHTML = selected;
        $("#subjectList").find(".subjectItem").addClass("label-success listItem").removeClass('subjectItem').hide();

    }
    function clearSubjects() {
        $("#subject").val("");
        items = [];
        getItems();
    }
    function getItems() {
        $.ajax({
            url: "subjects.php",
            success: function(data) {
                var stuff = JSON.parse(data);
                for(x in stuff) {
                    var item = new Item(stuff[x].subject, 'a'+x+'a');
                    items.push(item);
                }
                createList();
            },
            error: function(error) {
                console.log(JSON.stringify(error));
            }
        });
    }
    getItems();
    $("#subject").keyup(function() {
        if($(this).val() != "") {
            search($(this).val());
        } else {
            $("#notSelected").find(".subjectItem").hide();
        }
    });
    $("#clearSubjects").on('click',function() {
        $("#subject").val("");
        items = [];
        getItems();
    });
    $('body').on('click', '.subjectItem', function(data) {
        thisItemString = $(this).attr("data-subject");
        var thisItem = items.find(findItem);
        if(thisItem.added) {
            thisItem.added = false;
            $(this).slideUp();
            $("#subjectList").find("#"+thisItem.id).slideUp();
            $("#notSelected").find("#"+thisItem.id).fadeIn();
        } else {
            thisItem.added = true;
            $(this).slideUp();
            $("#subjectList").find("#"+thisItem.id).fadeIn();
            $("#isSelected").find("#"+thisItem.id).fadeIn();
        //   $(this).remove();
        }
        function findItem(item) {
        return item.subject == thisItemString;
    }
});

    // Personal Search
    $("#obitSearch").submit(function(event) {
        event.preventDefault();
        var firstName = $("#firstname").val();
        var lastName = $("#lastname").val();
        var searchType = $("#searchType").val();
        var data = {
            lastname: lastName,
            firstname: firstName,
            searchtype: searchType
        };
        $.ajax({
            type: 'GET',
            url: 'obits-search-api.php',
            dataType: 'json',
            data: data,
            success: function(data) {
                
                var currentRow = 1;
                var htmlString = '';
                if($("#searchType").val() == 'obituaries') {
                    obituaryList = new ObituaryList();
                    for(x in data) {
                        var obituary = new Obituary(data[x].lastname, data[x].firstname, data[x].birthdate, data[x].deathdate, data[x].obitdate, data[x].page, data[x].id);
                        obituaryList.addItem(obituary);
                        htmlString += obituary.tableRow(currentRow, false);
                        currentRow++;
                    }
                    htmlString = obituaryList.tableHead() + htmlString + obituaryList.tableFoot;
                } else {
                    weddingList = new WeddingList();
                    for(x in data) {
                        var wedding = new Wedding(data[x].lastname, data[x].firstname, data[x].announcement, data[x].weddingdate, data[x].articledate, data[x].page, data[x].id);
                        weddingList.addItem(wedding);
                        htmlString += wedding.tableRow(currentRow, false);
                        currentRow++;
                    }
                    htmlString = weddingList.tableHead() + htmlString + weddingList.tableFoot;
                }
                document.getElementById("resultsHere").innerHTML = htmlString;
                removePrintButtonIfNoPrints();
                $("#resultsModal").modal('show');
                $("#myTable").tablesorter(); 
            },
            error: function(error) {
                window.alert(error.responseText);
            }
        });
    });
    // Article Search
    $("#articleSearch").submit(function(event) {
        event.preventDefault();
        var thisHeadline = $("#headlineKeyword").val();
        var theseSubjects = [];
        var fromDate = $("#startyear").val();
        var toDate = $("#endyear").val();
        for(i in items) {
            if(items[i].added) {
                theseSubjects.push(items[i].subject);
            }
        }
        if(fromDate != '') {
            fromDate += '-01-01';
        }
        if(toDate != '') {
            toDate += '-12-31';
        }
        var thisData = { 
            headline: thisHeadline,
            subjects: JSON.stringify(theseSubjects),
            fromDate: fromDate,
            toDate: toDate
        };
        $.ajax({
            type: 'GET',
            url: 'news-search-api.php',
            dataType: 'json',
            data: thisData,
            success: function(data) {
                articleList = new ArticleList();
                var htmlString = '';
                var currentRow = 1;
                for(x in data) {
                    var article = new Article(data[x].subject, data[x].article, data[x].articledate, data[x].page, data[x].id);
                    articleList.addItem(article);
                    htmlString += article.tableRow(currentRow, false);
                    currentRow++;
                }
                htmlString = articleList.tableHead(printList.hasItems()) + htmlString + articleList.tableFoot;
                document.getElementById("resultsHere").innerHTML = htmlString;
                removePrintButtonIfNoPrints();
                $("#resultsModal").modal('show');
                $("#myTable").tablesorter({
                    headers: {
                        '.printAll' : {
                            sorter: false
                        }
                    }
                });
            },
            error: function(error) {
                window.alert(error.responseText);
            }
        });
    });
    
    
// Classes

// Obituary Class
    function Obituary(lastname, firstname, birthdate, deathdate, obitdate, page, id) {
        this.type = 'obituary';
        this.lastname = lastname;
        this.firstname = firstname;
        this.birthdate = birthdate;
        this.deathdate = deathdate;
        this.obitdate = obitdate;
        this.page = page;
        this.printed = function() {
            return printList.isPrinting(this.type, this.id);
        }
        this.index = -1;
        this.id = id;
        this.tableRow = function(currentRow, printQueue) {
            var disabled = '';
            if(this.printed()) {
                disabled = 'disabled';
            }
            if(printQueue) {
                var printButton = '<button type="button" class="removePrint btn btn-default btn-xs" data-id="' + this.id + '" data-type="obituary" id="' + this.index + '">Remove</button>';
            } else {
                var printButton = '<button type="button" class="print btn btn-default btn-xs ' + disabled + '" data-id="' + this.id + '" data-type="obituary" id="' + this.index + '">Add to Print</button>';
            }
            var string = '<tr><td>' + currentRow + '</td><td>' + this.lastname + '</td><td>' + this.firstname + '</td><td>' + this.birthdate + '</td><td>' + this.deathdate + '</td><td>' + this.obitdate + '</td><td>' + this.page + '</td><td>' + printButton + '</td></tr>';
            return string;        
        }
    }

    // Wedding Class
    function Wedding(lastname, firstname, announcement, weddingdate, articledate, page, id) {
        this.type = 'wedding';
        this.lastname = lastname;
        this.firstname = firstname;
        this.announcement = announcement;
        this.weddingdate = weddingdate;
        this.articledate = articledate;
        this.page = page;
        this.printed = function() {
            return printList.isPrinting(this.type, this.id);
        }
        this.index = -1;
        this.id = id;
        this.tableRow = function(currentRow, printQueue) {
            var disabled = '';
            if(this.printed()) {
                disabled = 'disabled';
            }
            if(printQueue) {
                var printButton = '<button type="button" class="removePrint btn btn-default btn-xs" data-id="' + this.id + '" data-type="wedding" id="' + this.index + '">Remove</button>';
            } else {
                var printButton = '<button type="button" class="print btn btn-default btn-xs ' + disabled + '" data-id="' + this.id + '" data-type="wedding" id="' + this.index + '">Add to Print</button>';
            }
            var string = '<tr><td>' + currentRow + '</td><td>' + this.lastname + '</td><td>' + this.firstname + '</td><td>' + this.announcement + '</td><td>' + this.weddingdate + '</td><td>' + this.articledate + '</td><td>' + this.page + '</td><td>' + printButton + '</td></tr>';
            return string;          
        }
    }
    // Article Class
    function Article(subject, headline, date, page, id) {
        this.type = 'article';
        this.subject = subject;
        this.headline = headline;
        this.date = date;
        this.page = page;
        this.printed = function() {
            return printList.isPrinting(this.type, this.id);
        }
        this.index = -1;
        this.id = id;
        this.tableRow = function(currentRow, printQueue) {
            var disabled = '';
            if(this.printed()) {
                disabled = 'disabled';
            }
            if(printQueue) {
                var printButton = '<button type="button" class="removePrint btn btn-default btn-xs" data-id="' + this.id + '" data-type="article" id="' + this.index + '">Remove</button>';
            } else {
                var printButton = '<button type="button" class="print btn btn-default btn-xs ' + disabled + '" data-id="' + this.id + '" data-type="article" id="' + this.index + '">Add to Print</button>';
            }
            var subjectName = this.subject;
            if(subjectName.length > 20) {
                subjectName = subjectName.substring(0,19) + '...';
            }
            var articleName = this.headline;
            if(articleName.length > 50) {
                articleName = articleName.substring(0, 49) + '...';
            }
            
            var string = '<tr><td>' + currentRow + '</td><td>' + subjectName + '</td><td>' + articleName + '</td><td>' + this.date + '</td><td>' + this.page + '</td><td>' + printButton + '</td></tr>';
            return string;
        }
    }
     // Article List class
    function ArticleList() {
        this.list = [];
        this.headers = ['Subject', 'Headline', 'Date', 'Page'];
        this.tableHead = function() {
            var string = '<button type="button" data-dismiss="modal" style="float:right;" class="btn btn-success printButton" id="viewPrints">View Prints</button>';            
            string += '<h3 class="text-center">Articles</h3>';
            string += '<p>' + this.list.length.toLocaleString('en') + ' Results</p><table class="table tablesorter" id="myTable"><thead><tr><th class="sort"> No.</th><th class="sort"> Subject</th><th class="sort"> Headline</th><th class="sort"> Date</th><th class="sort"> Page</th><td class="sorter-false"><button type="button" id="printAllArticles" class="btn btn-primary btn-xs">Print All</button></td></tr></thead><tbody>';
            return string;
        }
        this.tableFoot = '</tbody></table>';
        this.addItem = function(item) {
            item.index = this.list.length;
            this.list.push(item);
        }
    }
    function Item(subject, id) {
        this.subject = subject;
        this.id = id;
        this.added = false;
        this.card = function() {
            var string = '<span class="label subjectItem" id="' + this.id + '" data-subject="' + this.subject + '">' + this.subject + '</span>';
            return string;
        }
    }
     // Obituary List class
    function ObituaryList() {
        this.list = [];
        this.headers = ['Last Name', 'First Name', 'Birth Date', 'Death Date', 'Obituary Date', 'Page'];
        this.tableHead = function() {
            var string = '<button type="button" data-dismiss="modal" style="float:right;" class="btn btn-success printButton" id="viewPrints">View Prints</button>';            
            string += '<h3 class="text-center">Obituaries</h3>';
            string += '<p>' + this.list.length.toLocaleString('en') + ' Results</p><table class="table tablesorter" id="myTable"><thead><tr><th class="sort"> No.</th><th class="sort"> Last Name</th><th class="sort"> First Name</th><th class="sort"> Birth Date</th><th class="sort"> Death Date</th><th class="sort"> Obituary Date</th><th> Page</th><td class="sorter-false"><button type="button" id="printAllObits" class="btn btn-primary btn-xs">Print All</button></td></tr></thead><tbody>';
            return string;                     
        }
        this.tableFoot = '</tbody></table>';
        this.addItem = function(item) {
            item.index = this.list.length;
            this.list.push(item);
        }
    }
    // Wedding List class
    function WeddingList() {
        this.list = [];
        this.headers = ['Last Name', 'First Name', 'Announcement', 'Wedding Date', 'Article Date', 'Page'];
        this.tableHead = function() {
            var string = '<button type="button" data-dismiss="modal" style="float:right;" class="btn btn-success printButton" id="viewPrints">View Prints</button>';            
            string += '<h3 class="text-center">Weddings/Anniversaries</h3>';
            string += '<p>' + this.list.length.toLocaleString('en') + ' Results</p><table class="table tablesorter" id="myTable"><thead><tr><th class="sort"> No.</th><th class="sort"> Last Name</th><th class="sort"> First Name</th><th class="sort"> Announcement</th><th class="sort"> Wedding Date</th><th> Article Date</th><th> Page</th><td class="sorter-false"><button type="button" class="btn btn-primary btn-xs" id="printAllWeddings">Print All</button></td></tr></thead><tbody>';
            return string;       
        }
        this.tableFoot = '</tbody></table>';
        this.addItem = function(item) {
            item.index = this.list.length;
            this.list.push(item);
        }
    }

     // Print List class. Keeps track of what is printing
    function PrintList() {
        this.articleList = [];
        this.obituaryList = [];
        this.weddingList = [];
        this.hasItems = function() {
            if(this.articleList.length > 0 || this.weddingList.length > 0 || this.obituaryList.length > 0) {
                return true;
            } else {
                return false;
            }
        }
        this.removePrint = function(id, type) {
            if(type == 'article') {
                var index = this.articleList.findIndex(findItem);
                this.articleList.splice(index, 1);
            } else if(type == 'obituary') {
                var index = this.obituaryList.findIndex(findItem);
                this.obituaryList.splice(index, 1);
            } else {
                var index = this.weddingList.findIndex(findItem);
                this.weddingList.splice(index, 1);
            }
            function findItem(find) {
                return find.id == id;
            }
        }
        this.addPrint = function(item) {
            if(item.type == 'article') {
                if(this.articleList.findIndex(findItem) == -1) {
                    this.articleList.push(item);
                } else {
                    window.alert("You've already added this to the print queue");
                }
            } else if(item.type == 'obituary') {
                if(this.obituaryList.findIndex(findItem) == -1) {
                    this.obituaryList.push(item);
                } else {
                    window.alert("You've already added this to the print queue");
                }
            } else {
                if(this.weddingList.findIndex(findItem) == -1) {
                    this.weddingList.push(item);
                } else {
                    window.alert("You've already added this to the print queue");
                }
            }
            function findItem(find) {
                return find.id == item.id;
            }
        }
        this.clearPrints = function() {
            this.articleList = [];
            this.obituaryList = [];
            this.weddingList = [];
        }
        this.isPrinting = function(type, item) {
            if(type == 'article') {
                if(this.articleList.length > 0) {
                    if(this.articleList.findIndex(findItem) != -1) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else if(type == 'obituary') {
                if(this.obituaryList.length > 0) {
                    if(this.obituaryList.findIndex(findItem) != -1) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                if(this.weddingList.length > 0) {
                    if(this.weddingList.findIndex(findItem) != -1) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            }
            function findItem(find) {
                return find.id == item;
            }
        }
        this.sortLists = function() {
            debugger;
            this.articleList.sort(sortArticles);
            this.weddingList.sort(sortPersonal);
            this.obituaryList.sort(sortPersonal);
            function sortArticles(a, b) {
                if(a.subject.toUpperCase() == b.subject.toUpperCase()) {
                    var date1 = new Date(a.date);
                    var date2 = new Date(b.date);
                    if(date1 < date2) {
                        return -1;
                    } else {
                        return 1;
                    }
                } else {
                    if(a.subject.toUpperCase() < b.subject.toUpperCase()) {
                        return -1;
                    } else {
                        return 1;
                    }
                }
            }
            function sortPersonal(a, b) {
                if(a.lastname.toUpperCase() == b.lastname.toUpperCase()) {
                    if(a.firstname.toUpperCase() < b.firstname.toUpperCase()) {
                        return -1;
                    } else {
                        return 1;
                    }
                } else {
                    if(a.lastname.toUpperCase() < b.lastname.toUpperCase()) {
                        return -1;
                    } else {
                        return 1;
                    }
                }
            }
        }
    }



});
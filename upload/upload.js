$(document).ready(function() {
    // Classes

    function FileYear(year) {
        this.year = year;
        this.files = [];
        this.row = function(arrayIndex) {
            
            var containsObits = 'No';
            var containsNews = 'No';
            var containsWeddings = 'No';
            var fileIds = [];
            for(var i = 0; i < this.files.length; i++) {
                fileIds.push(this.files[i].id);
                if(this.files[i].type == 'Obituary') {
                    containsObits = 'Yes';
                }
                if(this.files[i].type == 'Weddings-Anniversaries') {
                    containsWeddings = 'Yes';
                }
                if(this.files[i].type == 'Article') {
                    containsNews = 'Yes';
                }
            }
            var string = '<tr><td><input data-row="' + arrayIndex + '" type="checkbox" id="removeFile"></td><td>' + this.files[0].uploaddate + '</td><td>' + this.year + '</td>';
            string += '<td>' + containsObits + '</td><td>' + containsNews + '</td><td>' + containsWeddings + '</td></tr>';
            return string;

        };
    }

    function File(filename, type, uploaddate, id, year) {
        this.year = year;
        this.filename = filename;
        this.type = type;
        this.uploaddate = uploaddate;
        this.id = id;
        this.toDelete = false;
        this.row = function() {
            var string = '<tr><td><input data-id="' + this.id + '" type="checkbox" id="removeFile" data-type="' + this.type + '" data-file="' + this.filename + '"></td><td>' + this.uploaddate + '</td><td>' + this.filename + '</td><td>' + this.type + '</td></tr>';
            return string;
        };
    }

    // Globals
    var fileList = [];
    var file;
    window.pollingPeriod = 500;
    window.progressInterval;
    // Load file list
    loadFileList();
    var fileYears = [];
    // functions
    function loadFileList() {
        $.ajax({
            url: "files.php",
            success: function(data) {
                var files = JSON.parse(data);
                fileYears = [];
                var htmlString = '<tr><th></th><th>Upload Date</th><th>Year</th><th>Obituaries</th><th>Articles</th><th>Weddings/Anniversaries</th></tr>';
                for(x in files) {
                    var yearExists = false;
                    var thisFile = new File(files[x].filename, files[x].type, files[x].uploaddate, files[x].id, files[x].year);
                    for(j in fileYears) {
                        if(thisFile.year == fileYears[j].year) {
                            yearExists = true;
                            fileYears[j].files.push(thisFile);
                        }
                    }
                    if(!yearExists) {
                        var thisYear = new FileYear(thisFile.year);
                        thisYear.files.push(thisFile);
                        fileYears.push(thisYear);
                    }
                }
                for(a in fileYears) {
                    htmlString += fileYears[a].row(a);
                }
                document.getElementById("filesTable").innerHTML = htmlString;
            }
        });
    }

    function uploadFinished() {
        clearInterval(window.progressInterval);
   //     $("#workingModal").modal('hide');
     //   $("#successModal").modal('show');
        document.getElementById('uploadForm').reset();
        $("#progressor").addClass('notransition');
        $("#progressor").width(0);
        loadFileList();
    }

    function uploadInProgress(year, type) {
      //  $("#workingModal").modal('hide');
      debugger;
      
      
    //    document.getElementById('progressor').width = '0%';

            document.getElementById("percentage").innerHTML = '0%';
            document.getElementById('workingTitle').innerHTML = year + ' ' + type;
            setTimeout(function(){
                window.progressInterval = setInterval(updateProgress, window.pollingPeriod);
                $("#progressor").removeClass('notransition');
            },100);
                
       // $("#progressor").width('0%');
      //  document.getElementById("progressor").style.width = '0%';
   //     document.getElementById("percentage").innerHTML = '0%';
      //  $("#processModal").modal('hide');
 //       document.getElementById('workingTitle').innerHTML = year + ' ' + type;
/*        $("#workingModal").modal({
            backdrop: 'static',
            keyboard: false
        });
        $("#workingModal").modal('show');*/
  //      window.progressInterval = setInterval(updateProgress, window.pollingPeriod);
   //  $("#progressor").removeClass("notransition");        
    }

    function updateProgress() {
        $.ajax({
            url: 'progress.json',
            success: function(data) {
                console.log(data.percentComplete + ' complete');
                document.getElementById("progressor").style.width = data.percentComplete + '%';
                document.getElementById("percentage").innerHTML = data.percentComplete + '%';
            },
            error: function(data) {
                console.log(JSON.stringify(data));
            }
        });
    }

    // Events
    $("#uploadFormSubmit").click(function() {
        file = document.getElementById('upload').files;
        if(file[0]) {
            var formData = new FormData();
            formData.append('xlsfile', file[0]);
            formData.append('year', $('#year').val());
            $.ajax({
                url: 'submitAPI.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(data) {
                    debugger;

                    /*$("#processModal").modal({
                        backdrop: 'static',
                        keyboard: false
                    });*/
                    processFiles($("#year").val());
                    /*$("#processModal").modal('show');
                    document.getElementById("processName").innerHTML = file[0].name;*/
                },
                error: function(data) {
                    window.alert(JSON.stringify(data));
                }
            });
            document.getElementById("progressor").style.width = '0%';
            document.getElementById("percentage").innerHTML = '0%';
            document.getElementById('workingTitle').innerHTML = 'Uploading File';
            $("#workingModal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $("#workingModal").modal('show');
        } else {
            window.alert("No file chosen");
        }
        
    });
    var articlesFinished = false;
    var weddingsFinished = false;
    var obitsFinished = false;
    function processFiles(year) {
        $.ajax({
            url: 'processNews.php',
            type: 'POST',
            data: {year: year},
            success: function(data) {
                uploadFinished();
            //   articlesFinished = true;
            //    finished();
                $.ajax({
                    url: 'process.php',
                    type: 'POST',
                    data: {year: year},
                    success: function(data) {
                        uploadFinished();
                      //  obitsFinished = true;
                     //   finished();
                        $.ajax({
                            url: 'processWeddings.php',
                            type: 'POST',
                            data: {year: year},
                            success: function(data) {
                                uploadFinished();
                                $("#workingModal").modal('hide');
                                $("#successModal").modal('show');
                            //    weddingsFinished = true;
                            //    finished();
                            },
                            error: function(data) {
                                clearInterval(window.progressInterval);
                                window.alert('error');
                                console.log(JSON.stringify(data));
                            }
                        });
                        uploadInProgress(year, 'Weddings-Anniversaries');  
                    },
                    error: function(data) {
                        clearInterval(window.progressInterval);
                        window.alert('error');
                        console.log(JSON.stringify(data));
                    }
                });
                uploadInProgress(year, 'Obituaries');
            },
            error: function(data) {
                clearInterval(window.progressInterval);
                window.alert('error');
                console.log(JSON.stringify(data));
            }
        });
        uploadInProgress(year, 'Articles');
        $("#workingModal").modal('show');
        
        
    }
    function finished() {
        if(articlesFinished && obitsFinished && weddingsFinished) {
            $("#workingModal").modal('hide');
            $("#successModal").modal('show');
            articlesFinished = false;
            weddingsFinished = false;
            obitsFinished = false;
        }
    }
    $("#articleButton").click(function() {
        $.ajax({
            url: 'processNews.php',
            type: 'POST',
            data: {filesent: file[0].name},
            success: function(data) {
                uploadFinished();
            },
            error: function(data) {
                clearInterval(window.progressInterval);
                window.alert('error');
                console.log(JSON.stringify(data));
            }
        });
        uploadInProgress();
    });
    
    $("#obitButton").click(function() {
        $.ajax({
            url: 'process.php',
            type: 'POST',
            data: {filesent: file[0].name},
            success: function(data) {
                uploadFinished();
            },
            error: function(data) {
                clearInterval(window.progressInterval);
                window.alert('error');
                console.log(JSON.stringify(data));
            }
        });
        uploadInProgress();
    });
    $("#weddingButton").click(function() {
        $.ajax({
            url: 'processWeddings.php',
            type: 'POST',
            data: {filesent: file[0].name},
            success: function(data) {
                debugger;
                uploadFinished();
            },
            error: function(data) {
                debugger;
                clearInterval(window.progressInterval);
                window.alert('error');
                console.log(JSON.stringify(data));
            }
        });
        uploadInProgress();  
    });
    $("#removeButton").click(function() {
        var deleteList = [];
        var list = $('#removeFile:checked').map(function() {
            return $(this).data('row');
        }).get();
        console.log(list);
        for(var j = 0; j < list.length; j++) {
            for(var k = 0; k < fileYears[list[j]].files.length; k++) {
                deleteList.push(fileYears[list[j]].files[k].id);
            }
        }
        console.log(deleteList);
        /*for(x in list) {

            var item = fileList.find(function(a) {
                return a.id == list[x];
            });
            deleteList.push(item);
        }*/
        $.ajax({
            url: 'remove.php',
            data: {ids: JSON.stringify(deleteList)},
            type: 'POST',
            success: function(data) {
                console.log(data);
                clearInterval(window.progressInterval);
                $("#workingModal").modal('hide');
                $("#successModal").modal('show');
                loadFileList();
            },
            error: function(error) {
                clearInterval(window.progressInterval);
                $("#workingModal").modal('hide');                
                window.alert("There was an error");
                loadFileList();
            }
        });
        uploadInProgress();
    });
});
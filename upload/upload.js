$(document).ready(function() {
    // Classes
    function File(filename, type, uploaddate, id) {
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

    // functions
    function loadFileList() {
        $.ajax({
            url: "files.php",
            success: function(data) {
                var files = JSON.parse(data);
                var htmlString = '<tr><th></th><th>Upload Date</th><th>File Name</th><th>Type</th></tr>';
                for(x in files) {
                    var thisFile = new File(files[x].filename, files[x].type, files[x].uploaddate, files[x].id);
                    fileList.push(thisFile);
                    htmlString += thisFile.row();
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

        debugger;
        $.ajax({
            url: 'processNews.php',
            type: 'POST',
            data: {filesent: year + '/articles' + year + '.csv'},
            success: function(data) {
                debugger;
                uploadFinished();
            //   articlesFinished = true;
            //    finished();
                $.ajax({
                    url: 'process.php',
                    type: 'POST',
                    data: {filesent: year + '/obituaries' + year + '.csv'},
                    success: function(data) {
                        debugger;
                        uploadFinished();
                      //  obitsFinished = true;
                     //   finished();
                        $.ajax({
                            url: 'processWeddings.php',
                            type: 'POST',
                            data: {filesent: year + '/weddings-anniversaries' + year + '.csv'},
                            success: function(data) {
                                debugger;
                                uploadFinished();
                                $("#workingModal").modal('hide');
                                $("#successModal").modal('show');
                            //    weddingsFinished = true;
                            //    finished();
                            },
                            error: function(data) {
                                debugger;
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
        debugger;
        var deleteList = [];
        var list = $('#removeFile:checked').map(function() {
            return $(this).data('id');
        }).get();
        for(x in list) {
            var item = fileList.find(function(a) {
                return a.id == list[x];
            });
            deleteList.push(item);
        }
        $.ajax({
            url: 'remove.php',
            data: {data: JSON.stringify(deleteList)},
            type: 'POST',
            success: function(data) {
                debugger;
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
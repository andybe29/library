<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Сайт - онлайн библиотека</title>
        <link rel="stylesheet" href="spectre.min.css">
    </head>
    <body>
        <div class="container" style="width: 128rem">
            <div class="columns">
                <div class="column col-12">
                    <h4 class="text-bold">Сайт - онлайн библиотека</h4>
                    <p><a href="https://github.com/andybe29/library">GitHub</a></p>
                </div>
            </div>
            <div class="form-horizontal">
                <div class="form-group">
                    <div class="col-4">
                        <input class="form-input" type="text" id="book" placeholder="название">
                    </div>
                    <div class="col-1"></div>
                    <div class="col-3">
                        <input class="form-input" type="text" id="writer" placeholder="автор">
                    </div>
                    <div class="col-1"></div>
                    <div class="col-3">
                        <input class="form-input" type="text" id="publisher" placeholder="издательство">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-12">
                        <button class="btn btn-block">найти</button>
                    </div>
                </div>
            </div>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>название</th>
                        <th>автор</th>
                        <th>год&nbsp;выхода</th>
                        <th>издательство</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
        <script>
            var settings = { loading : false };

            $(function() {
                $('input:text').eq(0).focus();

                $.ajaxSetup({
                    type       : 'post',
                    dataType   : 'json',
                    url        : 'ajax.php',
                    cache      : false,
                    timeout    : 300000,
                    beforeSend : function() { settings.loading = true; },
                    complete   : function() { settings.loading = false; },
                    error      : function(xhr, status) { if (status == 'timeout') alert('Превышено время ожидания ответа'); }
                });

                $('button').on('click', {}, search);
            });

            function search(e) {
                if (settings.loading) return;

                var post = {}, $out = $('tbody');
                $out.empty();

                $.each($('input:text'), function() {
                    post[$(this).attr('id')] = $.trim($(this).val());
                });

                if (post.book.length + post.publisher.length + post.writer.length == 0) {
                    alert('не указано, что искать');
                    return;
                }

                $.ajax({
                    data    : post,
                    success : function(data) {
                        if (data.ok) {
                            $.each(data.books, function(i) {
                                var h = [];
                                h.push('<tr>');
                                h.push('<td>' + (i + 1) + '</td>');
                                h.push('<td>' + this.name + '</td>');
                                h.push('<td><a href="writer.php?id=' + this.wid + '">' + this.writer + '</a></td>');
                                h.push('<td>' + (this.year ? this.year : '') + '</td>');
                                h.push('<td><a href="publisher.php?id=' + this.pid + '">' + this.publisher + '</a></td>');
                                h.push('<td><a href="' + this.file + '">скачать</a></td>');
                                h.push('</tr>');
                                $out.append(h.join(''));
                            });
                        } else {
                            alert(data.err);
                        }
                    }
                });
            }
        </script>
    </body>
</html>
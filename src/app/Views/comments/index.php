<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Комментарии</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background: #f4f6f8;
            padding: 30px 0;
        }

        .comment-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
            overflow-wrap: anywhere;
        }

        .comment-meta {
            color: #687385;
            font-size: 14px;
        }

        .form-card {
            background: white;
            border-radius: 10px;
            padding: 24px;
            margin-top: 30px;
        }

        .error-message {
            color: #dc3545;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="mb-4">Комментарии</h1>

    <div class="form-row mb-3">
        <div class="col-sm-6 mb-2">
            <label for="sort">Сортировка</label>
            <select id="sort" class="form-control">
                <option value="id"
                    <?= $sort === 'id' ? 'selected' : '' ?>>
                    По ID
                </option>
                <option value="date"
                    <?= $sort === 'date' ? 'selected' : '' ?>>
                    По дате
                </option>
            </select>
        </div>

        <div class="col-sm-6 mb-2">
            <label for="direction">Направление</label>
            <select id="direction" class="form-control">
                <option value="asc"
                    <?= $direction === 'asc' ? 'selected' : '' ?>>
                    По возрастанию
                </option>
                <option value="desc"
                    <?= $direction === 'desc' ? 'selected' : '' ?>>
                    По убыванию
                </option>
            </select>
        </div>
    </div>

    <div id="comments-list">
        <?= view('comments/_items', ['comments' => $comments]) ?>
    </div>

    <div id="comments-pager">
        <?= $pager->links() ?>
    </div>

    <section class="form-card">
        <h2 class="h4 mb-3">Добавить комментарий</h2>

        <div id="form-message" class="mb-3"></div>

        <form id="comment-form" action="<?= site_url('comments') ?>"
              method="post">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="name">Email</label>
                <input class="form-control" type="email" id="name"
                       name="name" maxlength="255" required>
                <div id="name-error" class="error-message"></div>
            </div>

            <div class="form-group">
                <label for="text">Комментарий</label>
                <textarea class="form-control" id="text" name="text"
                          rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label for="date">Дата комментария</label>
                <input class="form-control" type="date" id="date"
                       name="date" required>
            </div>

            <button class="btn btn-primary" type="submit">
                Добавить комментарий
            </button>
        </form>
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
$(function () {
    const feedUrl = <?= json_encode(site_url('comments/feed')) ?>;

    function queryParams(resetPage) {
        const params = new URLSearchParams(window.location.search);

        params.set('sort', $('#sort').val());
        params.set('direction', $('#direction').val());

        if (resetPage) {
            params.set('page', '1');
        }

        return params;
    }

    function loadComments(resetPage) {
        const params = queryParams(resetPage);

        return $.ajax({
            url: feedUrl + '?' + params.toString(),
            method: 'GET',
            dataType: 'json'
        }).done(function (data) {
            $('#comments-list').html(data.html);
            $('#comments-pager').html(data.pager);

            const next = '?' + params.toString();
            history.replaceState(null, '', next);
        });
    }

    $('#sort, #direction').on('change', function () {
        loadComments(true);
    });

    $(document).on('click', '#comments-pager a', function (event) {
        const href = $(this).attr('href');

        if (!href || href === '#') {
            return;
        }

        event.preventDefault();

        const url = new URL(href, window.location.origin);
        history.replaceState(null, '', url.search || '?');
        loadComments(false);
    });

    $('#name').on('input', function () {
        const email = this.value.trim();
        const valid = this.validity.valid && email !== '';

        $('#name-error').text(
            email !== '' && !valid ? 'Введите корректный email' : ''
        );
    });

    $('#comment-form').on('submit', function (event) {
        event.preventDefault();

        const form = this;
        const email = $('#name')[0];

        if (!email.value.trim() || !email.validity.valid) {
            $('#name-error').text('Введите корректный email');
            return;
        }

        $.ajax({
            url: form.action,
            method: 'POST',
            data: $(form).serialize(),
            dataType: 'json',

            success: function (response) {
                $('#form-message').html(
                    $('<div>').addClass('alert alert-success')
                        .text(response.message)
                );

                form.reset();
                $('#name-error').text('');

                loadComments(true);
            },

            error: function (xhr) {
                let message = 'Не удалось добавить комментарий';

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    message = Object.values(
                        xhr.responseJSON.errors
                    ).join('; ');
                } else if (xhr.status === 403) {
                    message = 'Ошибка проверки CSRF. Обновите страницу.';
                }

                $('#form-message').html(
                    $('<div>').addClass('alert alert-danger').text(message)
                );
            }
        });
    });

    $(document).on('click', '.delete-comment', function () {
        const id = $(this).data('id');

        if (!confirm('Удалить этот комментарий?')) {
            return;
        }

        $.ajax({
            url: '<?= site_url('comments/delete') ?>/' + id,
            method: 'POST',
            data: $('#comment-form').serialize(),
            dataType: 'json',

            success: function () {
                loadComments(false);
            },

            error: function (xhr) {
                alert(
                    xhr.responseJSON?.message ||
                    'Не удалось удалить комментарий'
                );
            }
        });
    });
});
</script>

</body>
</html>

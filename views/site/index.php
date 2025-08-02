<?php
$this->title = 'Сокращение ссылок';
?>

<h1>Сократить ссылку</h1>

<form id="shorten-form">
    <input type="text" name="url" id="url-input" placeholder="Вставь ссылку" style="width:300px;" required>
    <button type="submit">Сократить</button>
</form>

<div id="result" style="margin-top: 20px;"></div>

<script>
document.getElementById('shorten-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const url = document.getElementById('url-input').value;
    const resultDiv = document.getElementById('result');

    try {
        const response = await fetch('/site/shorten', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'url=' + encodeURIComponent(url)
        });

        const data = await response.json();
        if (data.short) {
            resultDiv.innerHTML = `Ваша ссылка: <a href="${data.short}" target="_blank">${data.short}</a>`;
        } else {
            resultDiv.innerHTML = `<span style="color:red">${data.error || 'Неизвестная ошибка'}</span>`;
        }
    } catch (err) {
        resultDiv.innerHTML = '<span style="color:red">Ошибка запроса</span>';
    }
});
</script>

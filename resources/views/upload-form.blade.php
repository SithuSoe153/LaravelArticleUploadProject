<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Article</title>
</head>

<body>
    <form action="{{ route('upload.submit') }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="text" name="title" placeholder="Article Title"><br>
        <input type="file" name="article" accept=".doc,.docx"><br>
        <input type="file" name="images[]" accept="image/*" multiple><br>
        <button type="submit">Upload Article</button>
    </form>
</body>

</html>

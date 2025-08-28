<form action="/import/medicine" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" required>
    <button type="submit">Import Medicines</button>
</form>


<form action="/import/supplier" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" required>
    <button type="submit">Import Supplier</button>
</form>


<input type="text" name="role" value="parent" id="role" hidden >
<div class="col-12">
  <input type="text" name="id_number" value="1111-2222" class="form-control" id="parentsId" hidden>

</div>

<div class="col-12">
  <label for="firstName" class="form-label">First Name</label>
  <input type="text" name="firstname" class="form-control" id="firstName" required>
  <div class="invalid-feedback">Please enter your first name!</div>
</div>

<div class="col-12">
  <label for="lastName" class="form-label">Last Name</label>
  <input type="text" name="lastname" class="form-control" id="lastName" required>
  <div class="invalid-feedback">Please enter your last name!</div>
</div>

<!-- Username -->
<div class="col-12">
  <label for="username" class="form-label">Username</label>
  <div class="input-group has-validation">
    <span class="input-group-text" id="inputGroupPrepend">@</span>
    <input type="text" name="username" class="form-control" id="username" required>
    <div class="invalid-feedback">Please choose a username.</div>
  </div>
</div>

<!-- Email -->
<div class="col-12">
  <label for="email" class="form-label">Email</label>
  <input type="email" name="email" class="form-control" id="email" required>
  <div class="invalid-feedback">Please enter a valid email address!</div>
</div>

<!-- Password -->
<div class="col-12">
  <label for="password" class="form-label">Password</label>
  <input type="password" name="password" class="form-control" id="password" required>
  <div class="invalid-feedback">Please enter your password!</div>
</div>

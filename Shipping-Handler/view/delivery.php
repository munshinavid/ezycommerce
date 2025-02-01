<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Delivery man Registration</title>
</head>
<body>

  <h2>Delivery man Registration</h2>
  <form action="../control/delivery_registration.php" method="POST" enctype="multipart/form-data" >
    <table>
      <tr>
        <td><label for="name">Full Name</label></td>
        <td><input type="text" id="name" name="name" ></td>
      </tr>
      <tr>
        <td><label for="gender">Gender</label></td>
        <td><input type="radio" id="gender" name="gender" value="male" >Male
        <input type="radio" id="gender" name="gender" value="female" >Female</td>
      </tr>
      <tr>
        <td><label for="email">Email</label></td>
        <td><input type="email" id="email" name="email" ></td>
      </tr>
      <tr>
        <td><label for="password">Password</label></td>
        <td><input type="password" id="password" name="password" ></td>
      </tr>
      <tr>
        <td><label for="confirm-password">Confirm Password</label></td>
        <td><input type="password" id="confirm-password" name="confirm_password" ></td>
      </tr>
      <tr>
        <td><label for="phone">Phone Number</label></td>
        <td><input type="tel" id="phone" name="phone" ></td>
      </tr>
      <tr>
        <td><label for="vehicle-type">Vehicle Type</label></td>
        <td>
          <select id="vehicle-type" name="vehicle_type" >
            <option value="bike">Bike</option>
            <option value="car">Car</option>
            <option value="van">Van</option>
            <option value="truck">Truck</option>
          </select>
        </td>
      </tr>
      <tr>
        <td><label for="license-number">NID Number</label></td>
        <td><input type="text" id="nid-number" name="nid_number" ></td>
      </tr>
      <tr>
        <td><label for="address">Address</label></td>
        <td><textarea id="address" name="address" rows="3"></textarea></td>
      </tr>
      <tr>
        <td><label for="photo">Upload Photo</label></td>
        <td><input type="file" id="photo" name="photo" accept=".png,.jpeg,.jpg" ></td>
      </tr>
      <tr>
        <td><label for="photo">Upload NID</label></td>
        <td><input type="file" id="nid" name="nid" accept=".png,.jpeg,.jpg" ></td>
      </tr>
      <tr>
        <td colspan="2" style="text-align: center;">
          <button type="submit">Submit</button>
        </td>
      </tr>
    </table>
  </form>
  <script src="../JS/validation.js"></script>
</body>
</html>

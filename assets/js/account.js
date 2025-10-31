document.addEventListener("DOMContentLoaded", () => {

  // ✅ Récupérer l'utilisateur sauvegardé par le script login
  let user = JSON.parse(localStorage.getItem("user"));

  // ✅ Si aucun utilisateur connecté → redirection
  if (!user) {
    alert("Vous devez d'abord vous connecter !");
    window.location.href = "login.html";
    return;
  }

  // ✅ Pré-remplir les champs à l'ouverture
  window.onload = () => {
    document.querySelector('input[name="firstName"]')?.value = user.name || "";
    document.querySelector('input[name="lastName"]')?.value = user.lastName || "";
    document.querySelector('input[type="email"]')?.value = user.email || "";
    document.querySelector('input[name="phone"]')?.value = user.phone || "";
    document.querySelector('input[name="address"]')?.value = user.address || "";

    // Sidebar (si présente)
    document.querySelector(".sidebar .name")?.textContent = `${user.name || ""} ${user.lastName || ""}`;
    document.querySelector(".sidebar .email")?.textContent = user.email || "";
  };

  // ✅ Bouton "Sauvegarder le profil"
  document.querySelector(".btn.btn-primary")?.addEventListener("click", () => {

    let updatedUser = {
      ...user,
      name: document.querySelector('input[name="firstName"]')?.value,
      lastName: document.querySelector('input[name="lastName"]')?.value,
      email: document.querySelector('input[type="email"]')?.value,
      phone: document.querySelector('input[name="phone"]')?.value,
      address: document.querySelector('input[name="address"]')?.value
    };

    const isModified = JSON.stringify(user) !== JSON.stringify(updatedUser);

    if (isModified) {
      localStorage.setItem("user", JSON.stringify(updatedUser));
      alert("✅ Profil mis à jour avec succès !");

      // Update live display
      document.querySelector(".sidebar .name")?.textContent = `${updatedUser.name} ${updatedUser.lastName || ""}`;
      document.querySelector(".sidebar .email")?.textContent = updatedUser.email;

      user = updatedUser; // update current memory
    } else {
      alert("ℹ️ Aucun changement détecté.");
    }
  });

  // ✅ Changer mot de passe
  document.querySelector("#security .btn.btn-primary")?.addEventListener("click", () => {
    const oldPassword = document.querySelector('#security input[placeholder="Mot de passe actuel"]')?.value;
    const newPassword = document.querySelector('#security input[placeholder="Nouveau mot de passe"]')?.value;
    const confirmPassword = document.querySelector('#security input[placeholder="Confirmer le nouveau mot de passe"]')?.value;

    if (oldPassword !== user.password) {
      alert("❌ Ancien mot de passe incorrect !");
      return;
    }
    if (newPassword !== confirmPassword) {
      alert("❌ Les mots de passe ne correspondent pas !");
      return;
    }
    if (newPassword === "") {
      alert("❌ Le mot de passe ne peut pas être vide !");
      return;
    }

    user.password = newPassword;
    localStorage.setItem("user", JSON.stringify(user));

    alert("✅ Mot de passe changé avec succès !");
  });

});

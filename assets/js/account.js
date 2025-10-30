// ✅ Récupérer l'utilisateur connecté
let currentUser = JSON.parse(localStorage.getItem("currentUser"));
let users = JSON.parse(localStorage.getItem("users")) || [];

// ✅ Si aucun utilisateur n'est connecté → redirection vers login
if (!currentUser) {
    alert("Vous devez d'abord vous connecter !");
    window.location.href = "login.html"; // change si ton fichier login a un autre nom
}

// ✅ Pré-remplir les champs du formulaire avec les données de l'utilisateur
window.onload = () => {
    // Récupérer l'utilisateur depuis localStorage
    let currentUser = JSON.parse(localStorage.getItem("currentUser")) || {name:"", email:"", phone:"", address:"", password:""};

    // Pré-remplir les champs du formulaire
    document.querySelector('input[name="firstName"]')?.value = currentUser.name;
    document.querySelector('input[name="lastName"]')?.value = currentUser.lastName || "";
    document.querySelector('input[type="email"]')?.value = currentUser.email;
    document.querySelector('input[name="phone"]')?.value = currentUser.phone || "";
    document.querySelector('input[name="address"]')?.value = currentUser.address || "";

    // Mettre à jour l'affichage de la sidebar
    document.querySelector(".sidebar .name").textContent = currentUser.name + " " + (currentUser.lastName || "");
    document.querySelector(".sidebar .email").textContent = currentUser.email;
};

// Fonction pour sauvegarder le profil
document.querySelector(".btn.btn-primary")?.addEventListener("click", () => {
    let users = JSON.parse(localStorage.getItem("users")) || [];
    let currentUser = JSON.parse(localStorage.getItem("currentUser")) || {email:""};

    // Récupérer les nouvelles valeurs du formulaire
    let updatedUser = {
        ...currentUser,
        name: document.querySelector('input[name="firstName"]')?.value,
        lastName: document.querySelector('input[name="lastName"]')?.value,
        email: document.querySelector('input[type="email"]')?.value,
        phone: document.querySelector('input[name="phone"]')?.value,
        address: document.querySelector('input[name="address"]')?.value,
        password: currentUser.password
    };

    // Vérifier si quelque chose a changé
    const isModified = JSON.stringify(currentUser) !== JSON.stringify(updatedUser);

    if(isModified){
        // Mettre à jour ou ajouter l'utilisateur dans users
        let found = false;
        users = users.map(user => {
            if(user.email === currentUser.email){
                found = true;
                return updatedUser;
            }
            return user;
        });
        if(!found) users.push(updatedUser);

        // Sauvegarder
        localStorage.setItem("users", JSON.stringify(users));
        localStorage.setItem("currentUser", JSON.stringify(updatedUser));

        alert("✅ Profil mis à jour avec succès !");
        // Mettre à jour l'affichage de la sidebar
        document.querySelector(".sidebar .name").textContent = updatedUser.name + " " + (updatedUser.lastName || "");
        document.querySelector(".sidebar .email").textContent = updatedUser.email;
    } else {
        alert("ℹ️ Aucun changement détecté, rien n'a été modifié.");
    }
});

// Fonction pour changer le mot de passe
document.querySelector("#security .btn.btn-primary")?.addEventListener("click", () => {
    let currentUser = JSON.parse(localStorage.getItem("currentUser")) || {};
    let users = JSON.parse(localStorage.getItem("users")) || [];

    const oldPassword = document.querySelector('#security input[placeholder="Mot de passe actuel"]')?.value;
    const newPassword = document.querySelector('#security input[placeholder="Nouveau mot de passe"]')?.value;
    const confirmPassword = document.querySelector('#security input[placeholder="Confirmer le nouveau mot de passe"]')?.value;

    if(oldPassword !== currentUser.password){
        alert("❌ Ancien mot de passe incorrect !");
        return;
    }
    if(newPassword !== confirmPassword){
        alert("❌ Les mots de passe ne correspondent pas !");
        return;
    }
    if(newPassword === ""){
        alert("❌ Le mot de passe ne peut pas être vide !");
        return;
    }

    // Mise à jour du mot de passe
    currentUser.password = newPassword;
    users = users.map(user => user.email === currentUser.email ? currentUser : user);

    localStorage.setItem("users", JSON.stringify(users));
    localStorage.setItem("currentUser", JSON.stringify(currentUser));

    alert("✅ Mot de passe changé avec succès !");
});


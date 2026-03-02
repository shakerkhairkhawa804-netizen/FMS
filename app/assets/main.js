console.log("Global JS Loaded");

document.addEventListener("DOMContentLoaded", function(){

    /* ======================
       Sidebar toggle
    ====================== */
    const menuBtn = document.getElementById("menu-toggle");
    const sidebar = document.querySelector(".sidebar");
    if(menuBtn && sidebar){
        menuBtn.addEventListener("click", ()=>{
            sidebar.classList.toggle("active");
        });
    }

    /* ======================
       Language AJAX switch
    ====================== */
    const langLinks = document.querySelectorAll(".lang-switch a");
    langLinks.forEach(link=>{
        link.addEventListener("click", e=>{
            e.preventDefault();
            fetch(link.href)
            .then(res=>{
                if(res.ok) location.reload();
            });
        });
    });

    /* ======================
       Notification dropdown
    ====================== */
    const notifyIcon = document.querySelector(".notify-icon");
    const notifyContent = document.querySelector(".notify-content");
    if(notifyIcon && notifyContent){
        notifyIcon.addEventListener("click", e=>{
            e.stopPropagation();
            notifyContent.classList.toggle("show");
        });
        window.addEventListener("click", ()=>{ notifyContent.classList.remove("show"); });
    }

    /* ======================
       Language dropdown
    ====================== */
    const langDropdown = document.querySelector(".language-dropdown");
    const langBtn = langDropdown?.querySelector(".dropbtn");
    const langContent = langDropdown?.querySelector(".dropdown-content");
    if(langBtn && langContent){
        langBtn.addEventListener("click", e=>{
            e.stopPropagation();
            langContent.classList.toggle("show");
        });
        window.addEventListener("click", ()=>{ langContent.classList.remove("show"); });
    }

    /* ======================
       Form validation
    ====================== */
    const forms = document.querySelectorAll("form");
    forms.forEach(form=>{
        const inputs = form.querySelectorAll("input[required], select[required], textarea[required]");
        inputs.forEach(input=>{
            input.addEventListener("input", ()=>validateField(input));
            input.addEventListener("change", ()=>validateField(input));
        });

        function validateField(field){
            if(!field.value.trim()){
                field.classList.add("invalid");
                field.classList.remove("valid");
            } else {
                field.classList.add("valid");
                field.classList.remove("invalid");
            }
        }

        form.addEventListener("submit", function(e){
            let valid = true;
            inputs.forEach(input=>{
                if(!input.value.trim()){
                    valid = false;
                    input.classList.add("invalid");
                }
            });

            // Amount must be >0
            const amountField = form.querySelector('input[name="amount"]');
            if(amountField && parseFloat(amountField.value) <= 0){
                alert("Amount must be >0");
                amountField.focus();
                valid = false;
            }

            if(!valid){
                e.preventDefault();
                alert("Please fill all required fields!");
            }
        });
    });

    /* ======================
       Delete confirmation
    ====================== */
    const deleteLinks = document.querySelectorAll("a.delete");
    deleteLinks.forEach(link=>{
        link.addEventListener("click", e=>{
            if(!confirm("Are you sure?")) e.preventDefault();
        });
    });

});
document.addEventListener("DOMContentLoaded", function() {
    const menuBtn = document.getElementById("menu-toggle");
    const sidebar = document.querySelector(".sidebar");

    if(menuBtn && sidebar){
        menuBtn.addEventListener("click", function() {
            sidebar.classList.toggle("active");
        });
    }
});
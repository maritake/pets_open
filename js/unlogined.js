{
    'use strict';
    document.addEventListener('DOMContentLoaded', function() {
        
       // ペット登録数
        document.getElementById('form_pet_yes').addEventListener('click', function() {
            document.getElementById('form_register_number').classList.remove('hidden');
        });        
        document.getElementById('form_pet_no').addEventListener('click', function() {
            document.getElementById('form_register_number').classList.add('hidden');
        });        


    });
}
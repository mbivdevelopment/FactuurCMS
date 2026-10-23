<div class="form-grid">
    <?= field('Voornaam', 'Voornaam', 'user', $customer->Voornaam, 'text', [], 'required') ?>
    <?= field('Achternaam', 'Achternaam', 'user', $customer->Achternaam, 'text', [], 'required') ?>
    <?= field('Bedrijfsnaam', 'Bedrijfsnaam', 'building', $customer->Bedrijfsnaam) ?>
    <?= field('Email', 'E-mailadres', 'mail', $customer->Email, 'email', [], 'required') ?>
    <?= field('Telefoon', 'Telefoon', 'phone', $customer->Telefoon) ?>
    <?= field('Adres', 'Adres', 'map', $customer->Adres) ?>
    <?= field('Postcode', 'Postcode', 'map', $customer->Postcode) ?>
    <?= field('Plaats', 'Plaats', 'map', $customer->Plaats) ?>
    <?= field('Land', 'Land', 'globe', $customer->Land ?: 'NL', 'select', ['NL' => 'Nederland', 'BE' => 'België', 'DE' => 'Duitsland', 'FR' => 'Frankrijk', 'GB' => 'Verenigd Koninkrijk']) ?>
    <?= field('KvkNummer', 'KvK-nummer', 'hash', $customer->KvkNummer) ?>
    <?= field('BtwNummer', 'Btw-nummer', 'hash', $customer->BtwNummer) ?>
    <?= field('Wachtwoord', $customer->id() ? 'Nieuw wachtwoord (optioneel)' : 'Wachtwoord (leeg = automatisch)', 'lock', '', 'password', [], 'autocomplete="new-password"') ?>
    <?= field('Notities', 'Interne notities', 'note', $customer->Notities, 'textarea') ?>
    <?php if ($customer->id()): ?><label class="switch"><input type="checkbox" name="Actief" value="1" <?= $customer->Actief ? 'checked' : '' ?>> Account actief (kan inloggen)</label><?php endif; ?>
</div>

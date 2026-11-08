UPDATE Producten
SET Omschrijving = CASE Slug
    WHEN 'smtp-relay-100' THEN 'Voor kleine websites'
    WHEN 'smtp-relay-250' THEN 'Voor groeiende websites'
    WHEN 'smtp-relay-1000' THEN 'Voor professionele verzending'
    WHEN 'smtp-relay-2500' THEN 'Voor grote verzendvolumes'
    ELSE Omschrijving
END,
BijgewerktOp = NOW()
WHERE Slug IN ('smtp-relay-100', 'smtp-relay-250', 'smtp-relay-1000', 'smtp-relay-2500');

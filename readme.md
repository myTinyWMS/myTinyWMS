# Article:
bestnr                              article_supplier -> order_number
hersteller                          article_supplier -> supplier_id -> name
artikelbezeichnung                  article -> name
type                                article -> category_id
bestand                             article -> quantity
mindbestand                         article -> min_quantity
verbrauch                           article -> usage_quantity
bestellzeitpunkt                    
maschinenzugehoerigkeit             ?
preis                               article_supplier -> price
bestellmenge                        article_supplier -> order_quantity
bemerkung                           article -> notes
gefahrenklasse                      ?
lieferzeit                          article_supplier -> delivery_time
status                              article -> status
barcode                             ?
entnahmemenge                       article -> issue_quantity
barcode_file                        ?
mail_to_supplier                    -
bestell_status                      ?
date                                
sort_id                             article -> sort_id
inventur                            article -> inventory
einheit                             article -> unit_id

# Supplier:
company_name                        supplier -> name
email                               supplier -> email
phone                               supplier -> phone
contact_person                      supplier -> contact_person
category_id                         -
text_for_mail                       ?
website                             supplier -> website  
comment                             supplier -> notes

# Category
name                                categories -> name
bemerkung                           categories -> notes
anzahl                              -

# material_log
user_name
time_stamp
type
material_id
count
ist_count
comment
status


INSERT INTO aplicacion (nombre, descripcion, id_tipo_vehiculo)
VALUES 
    /*Camion*/
    ('Toda Posición', 'Llantas de camión que pueden colocarse en cualquier posición',1),
    ('Tracción', 'Las llantas de tracción son los caballos de fuerza de su camión, diseñados exclusivamente para los ejes traccionales',1),    
    ('Off road', 'Las llantas todo terreno son llantas hechas para quienes se alejan de las carreteras pavimentadas.',1),  
    ('Toda posición / direccion', 'Las llantas de toda posición son neumáticos que están diseñados para funcionar en el eje de dirección de un vehículo, pero también pueden instalarse en los ejes de tracción y remolque',1), 
    /*Agricola*/
    ('R-1', 'La designación R-1 es la profundidad de la banda de rodamiento estándar.',3), 
    ('R-1W', 'La profundidad de la banda de rodamiento de un R-1W es al menos un 20 % superior al de un neumático R-1 del mismo tamaño.',3), 
    ('Implemento', 'Las llantas de implemento están hechas para una máxima reducción del suelo al tiempo que resisten cargas pesadas y un desgaste prolongado.',3), 
    ('F-2(3GUIAS)', 'Las llantas F-2 son neumáticos agrícolas diseñados para tractores con tracción en dos ruedas y dirección delantera.',3), 
    ('F-2M (4 Guias)', 'Las llantas F-2 son neumáticos agrícolas diseñados para tractores con tracción en dos ruedas y dirección delantera.',3), 
    ('F-2M (4 Guias)', 'Las llantas F-2 son neumáticos agrícolas diseñados para tractores con tracción en dos ruedas y dirección delantera.',3), 
    ('Camara', 'Es un elemento con forma toroidal con el que cuentan multitud de vehículos, y que se encargan de mantener el aire comprimido en el interior de los neumáticos.',3), 
    ('Corbata', 'Las llantas corbata son un tipo de corbata para llantas de uso industrial que se utilizan para proteger la cámara de la llanta de un camión.',3), 
    /*Industrial*/
    ('C1 Smooth', 'La C (Compactor/Compactador) para superficies suaves (Smoth) con 1 rodeado por un circulo',4), 
    ('E1 Ribbed', 'La E (Earthmover) indica un neumático para maquinaria de movimiento de tierra, y Ribbed (estriado) significa que tiene un diseño de superficie lisa, ideal para estabilidad en superficies pavimentadas o compactas.',4),
    ('E2 Traction', 'La E (Earthmover) indica que es un neumático para maquinaria de movimiento de tierra, y el número 2 señala un diseño de tracción adecuado para superficies sueltas.', 4),
    ('E3 Rock', 'La E (Earthmover) indica un neumático para maquinaria de movimiento de tierra, y el número 3 señala un diseño de banda de rodadura más profunda, ideal para terrenos mixtos y rocosos.', 4),
    ('E4 Rock (Deeptread)', 'La E (Earthmover) indica un neumático para maquinaria de movimiento de tierra, y el número 4 señala un diseño de banda de rodadura extra profunda, ideal para terrenos extremadamente duros y rocosos.', 4),
    ('E7 Flotation', 'La E (Earthmover) indica un neumático para maquinaria de movimiento de tierra, y el número 7 señala un diseño de flotación, ideal para terrenos blandos y arenosos, donde se requiere distribuir el peso para evitar hundimiento.', 4),
    ('G1 Ribbed', 'La G (Grader) indica un neumático para motoniveladoras, y Ribbed (estriado) significa que tiene un diseño de banda de rodadura lisa, ideal para ofrecer estabilidad y un manejo suave en superficies pavimentadas.', 4),
    ('G2 Traction', 'La G (Grader) indica un neumático para motoniveladoras, y Traction (tracción) significa que tiene un diseño de banda de rodadura con patrones que proporcionan un mayor agarre, ideal para terrenos sueltos y desiguales.', 4),
    ('G3 Rock', 'La G (Grader) indica un neumático para motoniveladoras, y Rock (rocoso) significa que tiene un diseño de banda de rodadura robusto, ideal para terrenos duros y rocosos, proporcionando tracción y durabilidad en condiciones difíciles.', 4),

    ('G4 Rock (Deep Tread)', 'La G (Grader) indica un neumático para motoniveladoras, y Deep Tread (banda de rodadura profunda) proporciona una excelente tracción en terrenos duros y rocosos, ideal para trabajos en condiciones exigentes.', 4),
    ('G5 Rock (Extra Deep Tread)', 'La G (Grader) indica un neumático para motoniveladoras, y Extra Deep Tread (banda de rodadura extra profunda) ofrece máxima tracción y durabilidad en terrenos muy difíciles y rocosos.', 4),
    ('L2 Traction', 'La L (Loader) indica un neumático para cargadoras y bulldozers, y Traction (tracción) significa que tiene un diseño de banda de rodadura optimizado para ofrecer un excelente agarre en superficies sueltas y desiguales.', 4),
    ('L3 Rock', 'La L (Loader) indica un neumático para cargadoras, y Rock (rocoso) proporciona un diseño de banda de rodadura robusto, ideal para condiciones rocosas, asegurando durabilidad y tracción efectiva.', 4),
    ('L4 Rock (Deep Tread)', 'La L (Loader) indica un neumático para cargadoras, diseñado para ofrecer un equilibrio entre tracción y estabilidad, adecuado para una variedad de superficies de trabajo.', 4),
    ('L5 Rock (Very Deep Tread)', 'La L (Loader) indica un neumático para cargadoras, y Deep Tread (banda de rodadura profunda) proporciona un excelente agarre y durabilidad en terrenos duros y rocosos.', 4),
    ('L3 S Smooth', 'La L (Loader) indica un neumático para cargadoras, y S Smooth (suave) significa que tiene un diseño de banda de rodadura liso, ideal para ofrecer un manejo suave y estabilidad en superficies pavimentadas.', 4),
    ('L4 S Smooth (Extra Deep Tread)', 'La L (Loader) indica un neumático para cargadoras, S Smooth (suave) con banda de rodadura extra profunda, diseñado para proporcionar un manejo suave en superficies pavimentadas mientras mantiene buena estabilidad.', 4),
    ('L5 S Smooth (Extra Deep Tread)', 'La L (Loader) indica un neumático para cargadoras, S Smooth (suave) con banda de rodadura extra profunda, diseñado para proporcionar un manejo suave en superficies pavimentadas mientras mantiene buena estabilidad.', 4),
    ('R-4', 'El R-4 es un neumático diseñado para cargadoras y maquinaria agrícola, con una banda de rodadura que proporciona tracción y estabilidad en terrenos duros y mixtos, ideal para el trabajo en condiciones variadas.', 4),
    ('SKS (Solid Steer Traction)', 'El SKS es un neumático sólido diseñado para proporcionar tracción en la dirección, ideal para manipuladores de materiales y maquinaria de construcción, ofreciendo resistencia a pinchazos y mayor durabilidad en entornos exigentes.', 4),
    ('F-3', 'El F-3 es un neumático para maquinaria agrícola, diseñado para ofrecer un equilibrio entre tracción y comodidad, ideal para su uso en campos y terrenos agrícolas, con una banda de rodadura que facilita el desplazamiento en diversas condiciones de terreno.', 4);

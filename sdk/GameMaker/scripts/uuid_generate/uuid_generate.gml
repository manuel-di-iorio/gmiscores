/// @function uuid_generate()
/// @description Generates a RFC 4122 compliant UUID v4.
/// @return {string} UUID v4
function uuid_generate() {
    gml_pragma("forceinline");
    
    // Randomize the seed
	var originalSeed = random_get_seed();
	randomize();

    // Precomputed hex ASCII codes
    static HEX = [
        ord("0"), ord("1"), ord("2"), ord("3"),
        ord("4"), ord("5"), ord("6"), ord("7"),
        ord("8"), ord("9"), ord("a"), ord("b"),
        ord("c"), ord("d"), ord("e"), ord("f")
    ];

    // Reusable output buffer
    static OUT = buffer_create(36, buffer_fixed, 1);
    buffer_seek(OUT, buffer_seek_start, 0);

    for (var i = 0; i < 16; i++) {
        // Insert dashes
        if (i == 4 || i == 6 || i == 8 || i == 10) {
            buffer_write(OUT, buffer_u8, ord("-"));
        }

        var b = irandom(255);

        // Set UUID version (byte 6)
        if (i == 6) {
            b = (b & $0F) | $40;
        }

        // Set UUID variant (byte 8)
        if (i == 8) {
            b = (b & $3F) | $80;
        }

        // Convert byte to hex
        buffer_write(OUT, buffer_u8, HEX[(b >> 4) & $F]);
        buffer_write(OUT, buffer_u8, HEX[b & $F]);
    }
    
    random_set_seed(originalSeed);
    buffer_seek(OUT, buffer_seek_start, 0);
    return buffer_read(OUT, buffer_string);
}
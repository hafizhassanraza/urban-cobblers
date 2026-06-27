"""Extract and prepare brand logos for the storefront."""

from pathlib import Path

import fitz
from PIL import Image

BRAND_BLACK = (17, 17, 17)
LOGO_DIR = Path(__file__).resolve().parents[1] / "public" / "images" / "logo"
NAV_PDF = Path.home() / "Downloads" / "URBAN COBBLERS Logo (1).pdf"
ASSETS_DIR = Path(__file__).resolve().parents[1].parent / ".cursor" / "projects" / "c-laragon-www-urban-cobblers" / "assets"


def is_near_black(r: int, g: int, b: int, threshold: int = 42) -> bool:
    return max(r, g, b) < threshold


def is_white(r: int, g: int, b: int) -> bool:
    return r > 210 and g > 210 and b > 210


def is_copper(r: int, g: int, b: int) -> bool:
    return r > 100 and g > 60 and b < 130 and r > b


def crop_and_pad(image: Image.Image, padding: int) -> Image.Image:
    bbox = image.getbbox()
    if bbox:
        image = image.crop(bbox)

    if not padding:
        return image

    padded = Image.new("RGBA", (image.width + padding * 2, image.height + padding * 2), (0, 0, 0, 0))
    padded.paste(image, (padding, padding), image)
    return padded


def remove_white_background(image: Image.Image, threshold: int = 248) -> None:
    pixels = image.load()
    width, height = image.size

    for y in range(height):
        for x in range(width):
            r, g, b, a = pixels[x, y]
            if r >= threshold and g >= threshold and b >= threshold:
                pixels[x, y] = (255, 255, 255, 0)


def content_bbox(image: Image.Image, white_threshold: int = 248) -> tuple[int, int, int, int] | None:
    pixels = image.load()
    width, height = image.size
    min_x, min_y = width, height
    max_x, max_y = 0, 0

    for y in range(height):
        for x in range(width):
            r, g, b, a = pixels[x, y]
            if a == 0:
                continue
            if r >= white_threshold and g >= white_threshold and b >= white_threshold:
                continue
            min_x = min(min_x, x)
            min_y = min(min_y, y)
            max_x = max(max_x, x)
            max_y = max(max_y, y)

    if max_x <= min_x or max_y <= min_y:
        return None

    return min_x, min_y, max_x + 1, max_y + 1


def extract_nav_logo_from_pdf(pdf_path: Path, dst: Path, zoom: float = 3.0) -> None:
    doc = fitz.open(pdf_path)
    page = doc[0]
    width, height = page.rect.width, page.rect.height

    # Horizontal wordmark sits in the lower-middle band of the artboard.
    clip = fitz.Rect(
        width * 0.015,
        height * 0.385,
        width * 0.985,
        height * 0.595,
    )

    pix = page.get_pixmap(matrix=fitz.Matrix(zoom, zoom), clip=clip, alpha=True)
    image = Image.frombytes("RGBA", [pix.width, pix.height], pix.samples)
    doc.close()

    remove_white_background(image)

    bbox = content_bbox(image, white_threshold=250)
    if bbox:
        image = image.crop(bbox)

    image = crop_and_pad(image, padding=10)
    image.save(dst, "PNG")
    print(f"Saved nav logo from PDF -> {dst} ({image.size[0]}x{image.size[1]})")


def dilate_mask(mask: list[list[bool]], iterations: int = 10) -> list[list[bool]]:
    height = len(mask)
    width = len(mask[0])

    for _ in range(iterations):
        updated = [row[:] for row in mask]
        for y in range(height):
            for x in range(width):
                if mask[y][x]:
                    continue
                for dy in (-1, 0, 1):
                    for dx in (-1, 0, 1):
                        ny, nx = y + dy, x + dx
                        if 0 <= ny < height and 0 <= nx < width and mask[ny][nx]:
                            updated[y][x] = True
                            break
                    if updated[y][x]:
                        break
        mask = updated

    return mask


def find_urban_bottom(image: Image.Image) -> int:
    pixels = image.load()
    width, height = image.size
    white_rows: list[int] = []

    for y in range(height):
        white_count = sum(
            1 for x in range(width)
            if pixels[x, y][3] > 0 and is_white(*pixels[x, y][:3])
        )
        if white_count > 80:
            white_rows.append(y)

    if not white_rows:
        return int(height * 0.75)

    urban_bottom = white_rows[0]
    for index in range(1, len(white_rows)):
        if white_rows[index] - white_rows[index - 1] > 20:
            urban_bottom = white_rows[index - 1]
            break
    else:
        urban_bottom = white_rows[-1]

    return urban_bottom


def whiten_cobblers_text(image: Image.Image) -> None:
    pixels = image.load()
    width, height = image.size
    cobblers_start = find_urban_bottom(image) + 20

    for y in range(cobblers_start, height):
        for x in range(width):
            r, g, b, a = pixels[x, y]
            if a > 0 and is_copper(r, g, b):
                pixels[x, y] = (255, 255, 255, 255)


def process_footer_logo(src: Path, dst: Path) -> None:
    image = Image.open(src).convert("RGBA")
    pixels = image.load()
    width, height = image.size

    for y in range(height):
        for x in range(width):
            r, g, b, _ = pixels[x, y]

            if is_near_black(r, g, b):
                pixels[x, y] = (0, 0, 0, 0)
            elif is_white(r, g, b):
                pixels[x, y] = (255, 255, 255, 255)
            else:
                pixels[x, y] = (r, g, b, 255)

    whiten_cobblers_text(image)
    image = crop_and_pad(image, padding=8)
    image.save(dst, "PNG")
    print(f"Saved {dst} ({image.size[0]}x{image.size[1]})")


def footer_source_path() -> Path:
    originals = list(ASSETS_DIR.glob("*logo_dark_footer*"))
    if originals:
        return originals[0]

    return LOGO_DIR / "footer.png"


def main() -> None:
    LOGO_DIR.mkdir(parents=True, exist_ok=True)

    if NAV_PDF.exists():
        extract_nav_logo_from_pdf(NAV_PDF, LOGO_DIR / "nav.png")
    else:
        print(f"Nav PDF not found: {NAV_PDF}")

    process_footer_logo(footer_source_path(), LOGO_DIR / "footer.png")
    print("Logo processing complete.")


if __name__ == "__main__":
    main()

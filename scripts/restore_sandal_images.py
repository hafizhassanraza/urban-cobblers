"""Restore sandal images padded by pad_sandal_images.py."""

from pathlib import Path

from PIL import Image

SANDALS_DIR = Path(__file__).resolve().parents[1] / "public" / "images" / "shoes" / "sandals"
SCALE = 0.78


def restore_image(path: Path) -> None:
    image = Image.open(path).convert("RGBA")
    width, height = image.size
    inner_width = int(width * SCALE)
    inner_height = int(height * SCALE)
    left = (width - inner_width) // 2
    top = (height - inner_height) // 2
    cropped = image.crop((left, top, left + inner_width, top + inner_height))
    restored = cropped.resize((width, height), Image.Resampling.LANCZOS)
    restored.save(path, optimize=True)
    print(f"Restored {path.name}")


def main() -> None:
    for path in sorted(SANDALS_DIR.glob("*.png")):
        restore_image(path)


if __name__ == "__main__":
    main()

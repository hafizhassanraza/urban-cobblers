"""Crop and split sandal product images for the shop catalog."""
from pathlib import Path

from PIL import Image

ASSETS = Path(
    r"C:\Users\usman\.cursor\projects\c-laragon-www-urban-cobblers\assets"
)
OUT = Path(r"C:\laragon\www\urban-cobblers\public\images\shoes\sandals")
SIZE = 800


def find_asset(uuid: str) -> Path:
    matches = list(ASSETS.glob(f"*{uuid}*"))
    if not matches:
        raise FileNotFoundError(f"No asset found for {uuid}")
    return matches[0]


def crop_to_square(img: Image.Image) -> Image.Image:
    w, h = img.size
    side = min(w, h)
    left = (w - side) // 2
    top = (h - side) // 2
    cropped = img.crop((left, top, left + side, top + side))
    return cropped.resize((SIZE, SIZE), Image.Resampling.LANCZOS)


def save_square(img: Image.Image, name: str) -> None:
    OUT.mkdir(parents=True, exist_ok=True)
    crop_to_square(img).save(OUT / name, optimize=True)


def split_pair(path: Path, left_name: str, right_name: str) -> None:
    img = Image.open(path).convert("RGB")
    w, h = img.size
    mid = w // 2
    gap = max(4, int(w * 0.015))
    left = img.crop((0, 0, mid - gap // 2, h))
    right = img.crop((mid + gap // 2, 0, w, h))
    save_square(left, left_name)
    save_square(right, right_name)


def main() -> None:
    navy = find_asset("132da33f")
    black_copper = find_asset("a7bbe05a")
    brown_burgundy = find_asset("f0ba1639")
    tan_burgundy = find_asset("8aba44ef")

    save_square(Image.open(navy).convert("RGB"), "sandal-01-navy.png")
    split_pair(black_copper, "sandal-02-black-croc.png", "sandal-03-copper-croc.png")
    split_pair(brown_burgundy, "sandal-04-brown-croc.png", "sandal-05-burgundy-ostrich.png")
    split_pair(tan_burgundy, "sandal-06-tan-ostrich.png", "sandal-07-burgundy-croc.png")

    print(f"Processed 7 sandal images into {OUT}")


if __name__ == "__main__":
    main()

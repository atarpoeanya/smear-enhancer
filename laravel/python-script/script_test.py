import sys


def thisIsATest(input: str)-> str:
  print(input)
  return input

if __name__ == '__main__':
  input_path = sys.argv[1]
  thisIsATest(input_path)